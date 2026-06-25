@extends('layouts.app')
@section('title', 'Buat Jurnal Manual')
@section('page_title', 'Buat Jurnal Manual')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Buat Jurnal Manual</h1>
    <p class="page-subtitle">Input transaksi akuntansi double-entry secara manual</p>
  </div>
  <a href="{{ route('pengurus.accounting.journals') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card" style="max-width:800px;" x-data="{
  lines: [{account_code:'',debit:0,credit:0,description:''},{account_code:'',debit:0,credit:0,description:''}],
  addLine() { this.lines.push({account_code:'',debit:0,credit:0,description:''}); },
  removeLine(i) { if(this.lines.length > 2) this.lines.splice(i,1); },
  totalDebit()  { return this.lines.reduce((s,l)=>s+(parseFloat(l.debit)||0),0); },
  totalCredit() { return this.lines.reduce((s,l)=>s+(parseFloat(l.credit)||0),0); },
  isBalanced()  { return Math.abs(this.totalDebit()-this.totalCredit()) < 0.01; },
  fmt(v) { return 'Rp '+Math.round(v).toLocaleString('id-ID'); }
}">
  <div class="card-header"><h3>📝 Form Jurnal</h3></div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('pengurus.accounting.journals.store') }}">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tanggal <span class="req">*</span></label>
          <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">Keterangan <span class="req">*</span></label>
          <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="e.g. Beban Operasional Kantor" required>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Journal Lines --}}
      <div style="margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
        <label class="form-label" style="margin:0;">Baris Jurnal (min. 2)</label>
        <button type="button" @click="addLine()" class="btn btn-secondary btn-sm">+ Tambah Baris</button>
      </div>

      <div class="table-wrapper" style="margin-bottom:12px;">
        <table>
          <thead>
            <tr>
              <th style="min-width:160px;">Kode Akun</th>
              <th>Keterangan Baris</th>
              <th style="min-width:140px;">Debit (Rp)</th>
              <th style="min-width:140px;">Kredit (Rp)</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(line, index) in lines" :key="index">
              <tr>
                <td>
                  <select :name="'lines['+index+'][account_code]'" class="form-control" x-model="line.account_code" required>
                    <option value="">— Pilih Akun —</option>
                    @foreach($accounts as $acc)
                      <option value="{{ $acc->code }}">{{ $acc->code }} — {{ $acc->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <input type="text" :name="'lines['+index+'][description]'" x-model="line.description" class="form-control" placeholder="Opsional">
                </td>
                <td>
                  <input type="number" :name="'lines['+index+'][debit]'" x-model="line.debit" class="form-control" min="0" step="1000" placeholder="0">
                </td>
                <td>
                  <input type="number" :name="'lines['+index+'][credit]'" x-model="line.credit" class="form-control" min="0" step="1000" placeholder="0">
                </td>
                <td>
                  <button type="button" @click="removeLine(index)" class="btn btn-danger btn-sm" x-show="lines.length > 2">✕</button>
                </td>
              </tr>
            </template>
            {{-- Totals Row --}}
            <tr style="background:var(--gray-50);font-weight:700;">
              <td colspan="2" style="text-align:right;color:var(--gray-500);font-size:13px;">TOTAL</td>
              <td class="money" :style="isBalanced() ? 'color:var(--success)' : 'color:var(--danger)'" x-text="fmt(totalDebit())"></td>
              <td class="money" :style="isBalanced() ? 'color:var(--success)' : 'color:var(--danger)'" x-text="fmt(totalCredit())"></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div x-show="!isBalanced()" class="alert alert-danger" style="display:none;">
        ⚠️ Jurnal tidak seimbang! Total Debit harus sama dengan Total Kredit.
      </div>
      <div x-show="isBalanced() && totalDebit() > 0" class="alert alert-success" style="display:none;">
        ✅ Jurnal seimbang — siap disimpan.
      </div>

      <div class="flex justify-end gap-2" style="margin-top:16px;">
        <a href="{{ route('pengurus.accounting.journals') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary" :disabled="!isBalanced() || totalDebit() <= 0">
          Simpan Jurnal
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
