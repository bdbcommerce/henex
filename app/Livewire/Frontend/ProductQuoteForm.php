<?php
namespace App\Livewire\Frontend;
use App\Models\Inquiry;
use Livewire\Attributes\On;
use Livewire\Component;
class ProductQuoteForm extends Component
{
    public bool $open = false;
    public int|null $productId = null;
    public string $productName = '';
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $message = '';
    public bool $sent = false;

    #[On('open-quote-modal')]
    public function openModal(int $productId, string $productName): void
    {
        $this->productId   = $productId;
        $this->productName = $productName;
        $this->sent        = false;
        $this->open        = true;
    }

    protected function rules(): array
    {
        return [
            'name'    => 'required|string|max:120',
            'phone'   => 'required|string|max:50',
            'email'   => 'nullable|email|max:120',
            'message' => 'nullable|string|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();
        Inquiry::create([
            'name'       => $this->name,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'message'    => $this->message ?: null,
            'product_id' => $this->productId,
            'locale'     => app()->getLocale(),
        ]);
        $this->reset(['name','phone','email','message']);
        $this->sent = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset(['name','phone','email','message','sent']);
    }

    public function render()
    {
        return view('livewire.frontend.product-quote-form');
    }
}
