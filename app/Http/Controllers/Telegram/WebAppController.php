<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Region;
use App\Models\TelegramUser;
use App\Services\InquiryNotifier;
use App\Telegram\TelegramApi;
use App\Telegram\WebAppAuth;
use Illuminate\Http\Request;

/** Telegram Mini App: catalog, product pages, resellers and quote requests. */
class WebAppController extends Controller
{
    public function __construct(Request $request)
    {
        $lang = $request->query('lang');
        app()->setLocale(in_array($lang, ['uz', 'ru', 'en'], true) ? $lang : 'uz');
    }

    public function home(Request $request)
    {
        $categories = Category::where('is_active', true)->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->with('media')->orderBy('sort_order')->get();

        $search = trim((string) $request->query('q'));
        $results = $search === '' ? collect() : Product::where('is_active', true)
            ->where(fn ($q) => $q->where('sku', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
            ->with('media')->orderBy('sort_order')->limit(30)->get();

        $featured = Product::where('is_active', true)->where('is_featured', true)->with('media')->orderBy('sort_order')->limit(8)->get();

        return view('telegram.home', compact('categories', 'featured', 'search', 'results'));
    }

    public function category(string $slug)
    {
        $category = Category::findBySlugOrRedirect($slug);   // old slugs 301 to the current URL
        abort_unless($category->is_active, 404);
        $products = $category->products()->where('is_active', true)->with('media')->orderBy('sort_order')->get();

        return view('telegram.category', compact('category', 'products'));
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['media', 'categories', 'attributeValues.attribute']);

        return view('telegram.product', compact('product'));
    }

    public function resellers()
    {
        $regions = Region::orderBy('sort_order')
            ->with(['resellers' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])->get();

        return view('telegram.resellers', compact('regions'));
    }

    /** Quote request from inside the Mini App — identity comes from signed initData. */
    public function inquiry(Request $request, InquiryNotifier $notifier, TelegramApi $tg)
    {
        $tgUser = WebAppAuth::user($request->input('init_data'));
        if (! $tgUser) {
            return response()->json(['ok' => false, 'error' => 'auth'], 401);
        }

        $user = TelegramUser::where('telegram_id', $tgUser['id'])->first();
        if (! $user?->isRegistered()) {
            return response()->json(['ok' => false, 'error' => 'not_registered'], 403);
        }

        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'message'    => 'required|string|min:3|max:2000',
        ]);

        $inquiry = Inquiry::create([
            'name'             => $user->full_name,
            'phone'            => $user->phone,
            'message'          => $data['message'],
            'product_id'       => $data['product_id'] ?? null,
            'locale'           => $user->locale ?? app()->getLocale(),
            'source'           => 'telegram',
            'telegram_user_id' => $user->id,
            'industry_id'      => $user->industry_id,
        ]);
        $notifier->notify($inquiry);

        app()->setLocale($user->locale ?? 'uz');
        $tg->sendMessage($user->telegram_id, __('bot.inquiry_sent', ['id' => $inquiry->id, 'phone' => $user->phone]));

        return response()->json(['ok' => true, 'id' => $inquiry->id]);
    }
}
