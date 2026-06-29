Complete the admin panel Livewire components with full CRUD functionality.

Admin panel architecture (from CLAUDE.md section 7):
- Layout: resources/views/layouts/admin.blade.php (sidebar + topbar)
- All components in app/Livewire/Admin/
- Auth protected: role:super_admin,editor middleware

For each entity (Products, Categories, Resellers, Articles, Slides, Inquiries, Settings, Users):

**Index components** must have:
- $search with wire:model.live.debounce.300ms
- $perPage, $sortField, $sortDir
- WithPagination trait
- Bulk delete with confirmation modal (x-data="{ confirmId: null }")
- Table with hover:bg-gray-50 rows

**Form components** must have:
- Locale tabs (uz/ru/en) via locale-tabs.blade.php partial
- Real-time Livewire validation
- save() method that uses setTranslations() for translatable fields
- Flash message on success (Alpine toast, 3s auto-dismiss)

**ProductForm** additionally needs:
- Specs repeater (addSpec/removeSpec methods)
- WithFileUploads for MediaLibrary gallery images
- Category multi-select (belongsToMany)

**Admin UI conventions:**
- Page bg: bg-gray-50
- Cards: bg-white shadow-sm rounded-xl
- Primary buttons: bg-brand text-white hover:bg-brand-dark
- All forms use @tailwindcss/forms plugin styles

Complete admin layout first (sidebar.blade.php + topbar.blade.php), then implement each entity's Index + Form pair.
