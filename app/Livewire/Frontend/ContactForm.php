<?php
namespace App\Livewire\Frontend;
use App\Models\Inquiry;
use App\Models\Product;
use Livewire\Component;
class ContactForm extends Component
{
    public string $name = '';
    public string $company = '';
    public string $phone = '';
    public string $email = '';
    public string $message = '';
    public string $product_id = '';
    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name'       => 'required|string|max:120',
            'company'    => 'nullable|string|max:120',
            'phone'      => 'required|string|max:50',
            'email'      => 'nullable|email|max:120',
            'product_id' => 'nullable|exists:products,id',
            'message'    => 'nullable|string|max:5000',
        ];
    }

    public function submit(): void
    {
        $this->validate();
        Inquiry::create([
            'name'       => $this->name,
            'company'    => $this->company ?: null,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'product_id' => $this->product_id ?: null,
            'message'    => $this->message ?: null,
            'locale'     => app()->getLocale(),
        ]);
        $this->reset(['name','company','phone','email','message','product_id']);
        $this->sent = true;
    }

    public function render()
    {
        $products = cache()->remember('products_select', 600, fn () =>
            Product::where('is_active', true)->orderBy('sort_order')->get(['id','name'])
        );
        return view('livewire.frontend.contact-form', compact('products'));
    }
}
