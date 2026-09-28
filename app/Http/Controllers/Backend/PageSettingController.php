<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FooterColumn;
use App\Models\FooterLink;
use App\Models\PageSetting;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSettingController extends Controller
{
    public function index(): View
    {
        return view('backend.page-settings.index', [
            'settings' => PageSetting::current(),
            'footerColumns' => FooterColumn::ordered()->with('links')->get(),
            'socialLinks' => SocialLink::ordered()->get()->keyBy('platform'),
            'platforms' => SocialLink::PLATFORMS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_description' => ['nullable', 'string'],
            'about_image' => ['nullable', 'image', 'max:4096'],
            'vision_title' => ['nullable', 'string', 'max:255'],
            'vision_description' => ['nullable', 'string'],
            'vision_image' => ['nullable', 'image', 'max:4096'],
            'mission_title' => ['nullable', 'string', 'max:255'],
            'mission_description' => ['nullable', 'string'],
            'mission_image' => ['nullable', 'image', 'max:4096'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'footer_tagline' => ['nullable', 'string', 'max:255'],
            'footer_icon' => ['nullable', 'image', 'max:2048'],
            'copyright_text' => ['nullable', 'string', 'max:1000'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_mobile' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'office_hours' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'url', 'max:2000'],
            'hero_script' => ['nullable', 'string', 'max:120'],
            'hero_title' => ['nullable', 'string', 'max:160'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_video_url' => ['nullable', 'url', 'max:1000'],
            'hero_image' => ['nullable', 'image', 'max:6144'],
            'hero_image_url' => ['nullable', 'url', 'max:1000'],
            'why_title' => ['nullable', 'string', 'max:160'],
            'why_description' => ['nullable', 'string', 'max:1000'],
            'why_image' => ['nullable', 'image', 'max:6144'],
            'why_image_url' => ['nullable', 'url', 'max:1000'],
            'stat_years' => ['nullable', 'integer', 'min:0'],
            'stat_travelers' => ['nullable', 'integer', 'min:0'],
            'stat_destinations' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'max:40'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:40'],
        ]);

        $settings = PageSetting::current();

        foreach (['about_image', 'vision_image', 'mission_image', 'footer_icon', 'hero_image', 'why_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('page-settings', 'public');
            } elseif (! empty($data[$field.'_url'])) {
                $data[$field] = $data[$field.'_url'];
            } else {
                unset($data[$field]);
            }

            unset($data[$field.'_url']);
        }

        $settings->update($data);

        return back()->with('status', 'Page settings updated.');
    }

    public function updateSocialLinks(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'social' => ['nullable', 'array'],
            'social.*' => ['nullable', 'url', 'max:255'],
        ]);

        $urls = $data['social'] ?? [];

        foreach (SocialLink::PLATFORMS as $platform => $label) {
            $url = trim($urls[$platform] ?? '');

            if ($url === '') {
                SocialLink::where('platform', $platform)->delete();

                continue;
            }

            SocialLink::updateOrCreate(['platform' => $platform], ['url' => $url]);
        }

        return back()->with('status', 'Social links updated.');
    }

    public function storeFooterColumn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'heading' => ['required', 'string', 'max:100'],
        ]);

        $data['position'] = (FooterColumn::max('position') ?? 0) + 1;

        FooterColumn::create($data);

        return back()->with('status', 'Footer column added.');
    }

    public function updateFooterColumn(Request $request, FooterColumn $footerColumn): RedirectResponse
    {
        $data = $request->validate([
            'heading' => ['required', 'string', 'max:100'],
        ]);

        $footerColumn->update($data);

        return back()->with('status', 'Footer column updated.');
    }

    public function destroyFooterColumn(FooterColumn $footerColumn): RedirectResponse
    {
        $footerColumn->delete();

        return back()->with('status', 'Footer column removed.');
    }

    public function storeFooterLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer_column_id' => ['required', 'exists:footer_columns,id'],
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
        ]);

        $data['position'] = (FooterLink::where('footer_column_id', $data['footer_column_id'])->max('position') ?? 0) + 1;

        FooterLink::create($data);

        return back()->with('status', 'Footer link added.');
    }

    public function updateFooterLink(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
        ]);

        $footerLink->update($data);

        return back()->with('status', 'Footer link updated.');
    }

    public function destroyFooterLink(FooterLink $footerLink): RedirectResponse
    {
        $footerLink->delete();

        return back()->with('status', 'Footer link removed.');
    }
}
