<?php

namespace App\Livewire\Admin\Attributes;

use App\Models\Attribute;
use Illuminate\Support\Str;
use Livewire\Component;

class AttributeForm extends Component
{
    public ?Attribute $attribute = null;

    public array $form = [
        'slug'          => '',
        'is_multiple'   => true,
        'is_filterable' => true,
        'is_active'     => true,
        'sort_order'    => 0,
        'name'          => ['uz' => '', 'ru' => '', 'en' => ''],
    ];

    /** [['id' => ?int, 'value' => ['uz'=>'','ru'=>'','en'=>'']], ...] */
    public array $values = [];

    public function mount(?Attribute $attribute = null): void
    {
        if ($attribute?->exists) {
            $this->attribute = $attribute;
            $this->form = array_merge($this->form, $attribute->only(['slug', 'is_multiple', 'is_filterable', 'is_active', 'sort_order']));
            $this->form['name'] = array_merge($this->form['name'], $attribute->getTranslations('name'));
            $this->values = $attribute->values
                ->map(fn ($v) => ['id' => $v->id, 'value' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $v->getTranslations('value'))])
                ->toArray();
        }

        if (! $this->values) {
            $this->addValue();
        }
    }

    public function addValue(): void
    {
        $this->values[] = ['id' => null, 'value' => ['uz' => '', 'ru' => '', 'en' => '']];
    }

    public function removeValue(int $i): void
    {
        array_splice($this->values, $i, 1);
    }

    public function moveValue(int $i, int $direction): void
    {
        $j = $i + $direction;
        if (isset($this->values[$i], $this->values[$j])) {
            [$this->values[$i], $this->values[$j]] = [$this->values[$j], $this->values[$i]];
        }
    }

    public function save(): void
    {
        $attributeId = $this->attribute?->id;

        $this->validate([
            'form.name.uz'    => 'required|min:1',
            'values.*.value.uz' => 'required',
        ], [
            'form.name.uz.required'      => 'Attribute name (Uzbek) is required.',
            'values.*.value.uz.required' => 'Value (Uzbek) is required.',
        ]);

        $this->attribute ??= new Attribute();
        $this->attribute->fill([
            'is_multiple'   => $this->form['is_multiple'],
            'is_filterable' => $this->form['is_filterable'],
            'is_active'     => $this->form['is_active'],
            'sort_order'    => (int) $this->form['sort_order'],
        ]);
        $this->attribute->setTranslations('name', $this->form['name']);
        $this->attribute->save();

        // Upsert values in place so existing product links survive; delete the removed ones.
        $keptIds = [];
        foreach ($this->values as $i => $row) {
            $value = $row['id'] ? $this->attribute->values()->find($row['id']) : null;
            $value ??= $this->attribute->values()->make();
            $value->sort_order = $i;
            $value->setTranslations('value', array_filter($row['value'], fn ($v) => $v !== ''));
            $value->save();
            $keptIds[] = $value->id;
        }
        $this->attribute->values()->whereNotIn('id', $keptIds)->delete();

        session()->flash('success', 'Attribute saved successfully.');
        $this->redirect(route('admin.attributes.index'));
    }

    public function render()
    {
        return view('livewire.admin.attributes.form')->layout('layouts.admin');
    }
}
