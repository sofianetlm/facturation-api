@php($current = app()->getLocale())

<div style="display:flex;gap:.75rem;align-items:center;margin-inline:.75rem;font-size:.875rem;">
    <a href="{{ route('locale.switch', 'fr') }}"
       style="{{ $current === 'fr' ? 'font-weight:700;text-decoration:underline;' : 'opacity:.7;' }}">FR</a>
    <a href="{{ route('locale.switch', 'ar') }}"
       style="{{ $current === 'ar' ? 'font-weight:700;text-decoration:underline;' : 'opacity:.7;' }}">عربية</a>
</div>