<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HeaderButton;
use App\Models\HeaderSetting;
use App\Models\NavMenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeaderSettingController extends Controller
{
    public function index(): View
    {
        return view('backend.header-settings.index', [
            'settings' => HeaderSetting::current(),
            'menuItems' => NavMenuItem::roots()->with('children.children.children.children')->get(),
            'buttons' => HeaderButton::ordered()->get(),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logo' => ['nullable', 'image', 'max:4096'],
            'background_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('header-settings', 'public');
        } else {
            unset($data['logo']);
        }

        HeaderSetting::current()->update($data);

        return back()->with('status', 'Header settings updated.');
    }

    public function storeMenuItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['nullable', 'in:_self,_blank'],
        ]);

        $data['target'] = $data['target'] ?? '_self';
        $data['sort_order'] = (NavMenuItem::whereNull('parent_id')->max('sort_order') ?? -1) + 1;

        NavMenuItem::create($data);

        return back()->with('status', 'Menu item added.');
    }

    public function updateMenuItem(Request $request, NavMenuItem $navMenuItem): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['nullable', 'in:_self,_blank'],
        ]);

        $data['target'] = $data['target'] ?? '_self';

        $navMenuItem->update($data);

        return back()->with('status', 'Menu item updated.');
    }

    public function destroyMenuItem(NavMenuItem $navMenuItem): RedirectResponse
    {
        $this->deleteMenuItemWithChildren($navMenuItem);

        return back()->with('status', 'Menu item removed.');
    }

    public function reorderMenuItems(Request $request): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        $this->processMenuOrder($request->input('order'), null, 0);

        return response()->json(['success' => true]);
    }

    public function storeButton(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['nullable', 'in:_self,_blank'],
            'style' => ['nullable', 'in:solid,outline'],
        ]);

        $data['target'] = $data['target'] ?? '_self';
        $data['style'] = $data['style'] ?? 'solid';
        $data['position'] = (HeaderButton::max('position') ?? -1) + 1;

        HeaderButton::create($data);

        return back()->with('status', 'Header button added.');
    }

    public function updateButton(Request $request, HeaderButton $headerButton): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['nullable', 'in:_self,_blank'],
            'style' => ['nullable', 'in:solid,outline'],
        ]);

        $data['target'] = $data['target'] ?? '_self';
        $data['style'] = $data['style'] ?? 'solid';

        $headerButton->update($data);

        return back()->with('status', 'Header button updated.');
    }

    public function destroyButton(HeaderButton $headerButton): RedirectResponse
    {
        $headerButton->delete();

        return back()->with('status', 'Header button removed.');
    }

    public function reorderButtons(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($data['order'] as $index => $id) {
            HeaderButton::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function deleteMenuItemWithChildren(NavMenuItem $item): void
    {
        foreach ($item->children as $child) {
            $this->deleteMenuItemWithChildren($child);
        }

        $item->delete();
    }

    private function processMenuOrder(array $items, ?int $parentId, int $depth): void
    {
        foreach ($items as $index => $item) {
            NavMenuItem::where('id', (int) $item['id'])->update([
                'parent_id' => $parentId,
                'sort_order' => $index,
                'depth' => $depth,
            ]);

            if (! empty($item['children'])) {
                $this->processMenuOrder($item['children'], (int) $item['id'], $depth + 1);
            }
        }
    }
}
