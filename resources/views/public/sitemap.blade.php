<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $url)<url><loc>{{ $url }}</loc></url>@endforeach
@foreach($jobs as $job)<url><loc>{{ route('jobs.show', $job) }}</loc><lastmod>{{ $job->updated_at->toDateString() }}</lastmod></url>@endforeach
@foreach($companies as $company)<url><loc>{{ route('companies.show', $company) }}</loc><lastmod>{{ $company->updated_at->toDateString() }}</lastmod></url>@endforeach
</urlset>
