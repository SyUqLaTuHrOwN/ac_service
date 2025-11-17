<?php

namespace App\Livewire\Admin\Feedbacks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;

class Index extends Component
{
    use WithPagination;

    public string $q = '';
    public ?int $rating = null;
    public ?string $vis = null; // 'published' | 'hidden' | null

    public function updatingQ(){ $this->resetPage(); }
    public function updatingRating(){ $this->resetPage(); }
    public function updatingVis(){ $this->resetPage(); }

    public function togglePublish(int $id): void
    {
        $f = Feedback::findOrFail($id);
        $f->is_public  = ! $f->is_public;
        $f->approved_at = $f->is_public ? now() : null;
        $f->save();

        session()->flash('ok', $f->is_public ? 'Feedback dipublikasikan.' : 'Feedback disembunyikan.');
    }

    public function delete(int $id): void
    {
        Feedback::findOrFail($id)->delete();
        session()->flash('ok','Feedback dihapus.');
        $this->resetPage();
    }

    public function render()
    {
        $q = Feedback::with(['clientUser','report.schedule.client','report.schedule.location'])
            ->when($this->q, fn($qq)=> $qq->where('comment','like','%'.$this->q.'%'))
            ->when($this->rating, fn($qq)=> $qq->where('rating',$this->rating))
            ->when($this->vis==='published', fn($qq)=> $qq->published())
            ->when($this->vis==='hidden', fn($qq)=> $qq->where(function($x){
                $x->where('is_public',false)->orWhereNull('approved_at');
            }))
            ->latest();

        $items = $q->paginate(12);

        return view('livewire.admin.feedbacks.index', compact('items'))
            ->layout('layouts.app', [
                'title'=>'Ulasan Pelanggan',
                'header'=>'Operasional • Ulasan Pelanggan',
            ]);
    }
}
