<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Faq;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\NewsletterSubscriber;
use App\Models\PlatformSetting;
use App\Models\Report;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminContentController extends Controller
{
    public function lookups(Request $request)
    {
        return view('admin.lookups.index', [
            'industries' => Industry::orderBy('name')->get(),
            'categories' => JobCategory::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->paginate(30),
        ]);
    }

    public function storeLookup(Request $request, string $type)
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);
        $model = match ($type) {
            'industry' => Industry::class,
            'category' => JobCategory::class,
            'location' => Location::class,
            'skill' => Skill::class,
            default => abort(404),
        };
        $model::create(['name' => $request->name, 'slug' => Str::slug($request->name).'-'.Str::random(4)]);

        return back()->with('status', ucfirst($type).' added.');
    }

    public function destroyLookup(Request $request, string $type, int $id)
    {
        $model = match ($type) {
            'industry' => Industry::class,
            'category' => JobCategory::class,
            'location' => Location::class,
            'skill' => Skill::class,
            default => abort(404),
        };
        $model::findOrFail($id)->delete();

        return back()->with('status', ucfirst($type).' deleted.');
    }

    public function content(Request $request)
    {
        return view('admin.content.index', [
            'articles' => Article::latest()->paginate(10, ['*'], 'articles_page'),
            'faqs' => Faq::orderBy('sort_order')->get(),
            'testimonials' => Testimonial::orderBy('sort_order')->get(),
            'subscribers' => NewsletterSubscriber::latest()->paginate(10, ['*'], 'subscribers_page'),
        ]);
    }

    public function storeArticle(Request $request)
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:60'], 'excerpt' => ['nullable', 'string', 'max:500'], 'body' => ['required', 'string'], 'is_published' => ['boolean']]);
        Article::create($data + ['slug' => Str::slug($data['title']).'-'.Str::random(4), 'author_id' => $request->user()->id, 'published_at' => now()]);

        return back()->with('status', 'Article created.');
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();

        return back()->with('status', 'Article deleted.');
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:500'], 'answer' => ['required', 'string'], 'audience' => ['required', 'in:all,candidate,employer']]);
        Faq::create($data + ['sort_order' => Faq::max('sort_order') + 1]);

        return back()->with('status', 'FAQ added.');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();

        return back()->with('status', 'FAQ deleted.');
    }

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'role_title' => ['nullable', 'string', 'max:255'], 'company' => ['nullable', 'string', 'max:255'], 'quote' => ['required', 'string'], 'rating' => ['required', 'integer', 'min:1', 'max:5']]);
        Testimonial::create($data + ['avatar_initials' => strtoupper(substr($data['name'], 0, 2))]);

        return back()->with('status', 'Testimonial added.');
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('status', 'Testimonial deleted.');
    }

    public function reports(Request $request)
    {
        $reports = Report::with(['reporter', 'reviewedBy'])->latest()->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function resolveReport(Request $request, Report $report)
    {
        $data = $request->validate(['status' => ['required', 'in:resolved,dismissed'], 'resolution_notes' => ['nullable', 'string', 'max:1000']]);
        $report->update($data + ['reviewed_by_id' => $request->user()->id]);
        AuditLog::record($request->user(), 'report.'.$data['status'], $report, ['status' => $data['status']]);

        return back()->with('status', 'Report '.$data['status'].'.');
    }

    public function audit(Request $request)
    {
        $logs = AuditLog::with('user')->latest()->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }

    public function settings()
    {
        $settings = PlatformSetting::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate(['site_name' => ['required', 'string', 'max:100'], 'site_tagline' => ['nullable', 'string', 'max:255'], 'support_email' => ['required', 'email'], 'jobs_require_moderation' => ['boolean'], 'max_resume_size_kb' => ['required', 'integer', 'min:512', 'max:10240']]);
        foreach ($data as $k => $v) {
            PlatformSetting::set($k, $k === 'jobs_require_moderation' ? ($request->boolean('jobs_require_moderation') ? '1' : '0') : (string) $v);
        }
        Cache::forget('home_stats');

        return back()->with('status', 'Platform settings saved.');
    }

    public function exportUsers()
    {
        $rows = [['Name', 'Email', 'Role', 'Verified', 'Suspended', 'Created']];
        foreach (User::orderBy('id')->get() as $u) {
            $rows[] = [$u->name, $u->email, $u->role, $u->email_verified_at ? 'yes' : 'no', $u->is_suspended ? 'yes' : 'no', $u->created_at->toDateTimeString()];
        }

        return $this->csv('talentflow-users.csv', $rows);
    }

    public function exportJobs()
    {
        $rows = [['Title', 'Company', 'Status', 'Employment', 'Workplace', 'Applications', 'Published']];
        foreach (Job::with('company')->orderBy('id')->get() as $j) {
            $rows[] = [$j->title, $j->company->name, $j->status, $j->employment_type, $j->workplace_type, $j->applications()->count(), $j->published_at?->toDateString() ?? ''];
        }

        return $this->csv('talentflow-jobs.csv', $rows);
    }

    protected function csv(string $filename, array $rows)
    {
        $handle = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=$filename"]);
    }
}
