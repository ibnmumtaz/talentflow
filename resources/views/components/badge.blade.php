@props(['status'])
@php
$map = [
  'submitted' => 'bg-sky-400/15 text-sky-300',
  'under_review' => 'bg-indigo-400/15 text-indigo-300',
  'shortlisted' => 'bg-violet-400/15 text-violet-300',
  'interview_scheduled' => 'bg-cyan-400/15 text-cyan-300',
  'interview_completed' => 'bg-blue-400/15 text-blue-300',
  'offer_extended' => 'bg-amber-400/15 text-amber-300',
  'hired' => 'bg-emerald-400/15 text-emerald-300',
  'rejected' => 'bg-rose-400/15 text-rose-300',
  'withdrawn' => 'bg-slate-400/15 text-slate-300',
  'draft' => 'bg-slate-400/15 text-slate-300',
  'pending' => 'bg-amber-400/15 text-amber-300',
  'published' => 'bg-emerald-400/15 text-emerald-300',
  'closed' => 'bg-slate-400/15 text-slate-300',
  'archived' => 'bg-slate-400/15 text-slate-300',
  'verified' => 'bg-emerald-400/15 text-emerald-300',
  'active' => 'bg-emerald-400/15 text-emerald-300',
  'open' => 'bg-amber-400/15 text-amber-300',
  'resolved' => 'bg-emerald-400/15 text-emerald-300',
  'scheduled' => 'bg-cyan-400/15 text-cyan-300',
  'completed' => 'bg-emerald-400/15 text-emerald-300',
  'cancelled' => 'bg-rose-400/15 text-rose-300',
];
$cls = $map[$status] ?? 'bg-white/10 text-slate-200';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold $cls"]) }}>{{ ucwords(str_replace('_', ' ', $status)) }}</span>
