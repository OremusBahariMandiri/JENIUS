@extends('layouts.app')

@section('title', 'Edit Contract')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800">Edit Contract</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">Contract</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Contract Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contract.update', $contract->id_md_cont) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contract No. <span class="text-danger">*</span></label>
                                <input type="text" name="no_contract" class="form-control @error('no_contract') is-invalid @enderror" value="{{ old('no_contract', $contract->no_contract) }}" required>
                                @error('no_contract')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contract Name <span class="text-danger">*</span></label>
                                <input type="text" name="contract" class="form-control @error('contract') is-invalid @enderror" value="{{ old('contract', $contract->contract) }}" required>
                                @error('contract')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="id_md_cust" class="form-select @error('id_md_cust') is-invalid @enderror" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id_md_cust }}" {{ old('id_md_cust', $contract->id_md_cust) == $customer->id_md_cust ? 'selected' : '' }}>
                                    {{ $customer->code }} - {{ $customer->customer }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_md_cust')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expenditure Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">IDR</span>
                                <input type="text" id="expenditure_display" class="form-control @error('expenditure') is-invalid @enderror" value="{{ old('expenditure') ? number_format(old('expenditure'), 2, ',', '.') : number_format($contract->expenditure, 2, ',', '.') }}" required>
                                <input type="hidden" name="expenditure" id="expenditure_value" value="{{ old('expenditure', $contract->expenditure) }}">
                                @error('expenditure')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_start" class="form-control @error('date_start') is-invalid @enderror" value="{{ old('date_start', $contract->date_start->format('Y-m-d')) }}" required>
                                @error('date_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_end" class="form-control @error('date_end') is-invalid @enderror" value="{{ old('date_end', $contract->date_end->format('Y-m-d')) }}" required>
                                @error('date_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $contract->note) }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                            <a href="{{ route('contract.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Script master Contract Edit --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const expenditureDisplay = document.getElementById('expenditure_display');
    const expenditureValue = document.getElementById('expenditure_value');

    // Format number to Rupiah format (1.000.000,00)
    function formatRupiah(value) {
        // Remove all non-numeric characters except comma
        let number = value.replace(/[^\d,]/g, '');

        // Remove existing dots (thousand separator)
        number = number.replace(/\./g, '');

        if (number === '') return '';

        // Split by comma for decimal handling
        let parts = number.split(',');
        let integerPart = parts[0];
        let decimalPart = parts.length > 1 ? parts[1] : '';

        // Limit decimal to 2 digits
        if (decimalPart.length > 2) {
            decimalPart = decimalPart.substring(0, 2);
        }

        // Add thousand separator to integer part
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Return with decimal if comma exists
        if (parts.length > 1) {
            return integerPart + ',' + decimalPart;
        } else {
            return integerPart + ',00';
        }
    }

    // Parse formatted Rupiah to number
    function parseRupiah(value) {
        if (value === '') return '';
        let cleaned = value.replace(/\./g, '').replace(',', '.');
        return cleaned;
    }

    expenditureDisplay.addEventListener('input', function(e) {
        // Simpan posisi cursor
        let cursorPosition = this.selectionStart;
        let beforeCursor = this.value.substring(0, cursorPosition);

        // Format value
        let formatted = formatRupiah(this.value);
        this.value = formatted;

        // Restore cursor position
        if (!beforeCursor.includes(',')) {
            // Cursor sebelum koma - hitung berdasarkan jumlah digit
            let digitsBeforeCursor = beforeCursor.replace(/\D/g, '').length;
            let newPos = 0;
            let digitCount = 0;
            for (let i = 0; i < this.value.length; i++) {
                if (/\d/.test(this.value[i])) {
                    digitCount++;
                    if (digitCount === digitsBeforeCursor) {
                        newPos = i + 1;
                        break;
                    }
                }
            }
            this.setSelectionRange(newPos, newPos);
        } else {
            // Cursor setelah koma - posisikan di desimal
            let commaPos = this.value.indexOf(',');
            let decimalDigitsInput = beforeCursor.split(',')[1] || '';
            let decimalDigits = decimalDigitsInput.length;
            let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
            this.setSelectionRange(newPos, newPos);
        }

        // Update hidden input
        expenditureValue.value = parseRupiah(formatted);
    });

    // Handle backspace dan delete
    expenditureDisplay.addEventListener('keydown', function(e) {
        let cursorPosition = this.selectionStart;
        let selectionEnd = this.selectionEnd;
        let commaPos = this.value.indexOf(',');

        if (e.key === 'Backspace') {
            // Jika ada selection (blok text), biarkan default behavior
            if (cursorPosition !== selectionEnd) {
                return;
            }

            // Jika backspace di area desimal
            if (commaPos !== -1 && cursorPosition > commaPos + 1) {
                e.preventDefault();

                let posInDecimal = cursorPosition - commaPos - 1;
                let beforeComma = this.value.substring(0, commaPos);
                let afterComma = this.value.substring(commaPos + 1);

                // Hapus karakter sebelum cursor di area desimal
                let newDecimal = afterComma.substring(0, posInDecimal - 1) + afterComma.substring(posInDecimal);

                // Rebuild - JANGAN hapus koma, biarkan tetap ada
                let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                let formatted = formatRupiah(newValue);
                this.value = formatted;

                // Set cursor
                let newCommaPos = this.value.indexOf(',');
                let newCursorPos = newCommaPos + Math.max(1, posInDecimal);
                this.setSelectionRange(newCursorPos, newCursorPos);

                expenditureValue.value = parseRupiah(this.value);
            }
            // Jika backspace tepat setelah koma (posisi pertama desimal)
            else if (commaPos !== -1 && cursorPosition === commaPos + 1) {
                // Jangan lakukan apa-apa, biarkan koma tetap ada
                e.preventDefault();
            }
            // Jika backspace pada posisi koma
            else if (cursorPosition === commaPos) {
                e.preventDefault();
                let beforeComma = this.value.substring(0, commaPos);
                this.value = beforeComma;
                this.setSelectionRange(beforeComma.length, beforeComma.length);
                expenditureValue.value = parseRupiah(this.value);
            }
        } else if (e.key === 'Delete') {
            // Jika ada selection (blok text), biarkan default behavior
            if (cursorPosition !== selectionEnd) {
                return;
            }

            // Jika cursor tepat sebelum koma
            if (cursorPosition === commaPos) {
                e.preventDefault();
                let beforeComma = this.value.substring(0, commaPos);
                this.value = beforeComma;
                this.setSelectionRange(beforeComma.length, beforeComma.length);
                expenditureValue.value = parseRupiah(this.value);
                return;
            }

            // Jika di area desimal
            if (commaPos !== -1 && cursorPosition > commaPos && cursorPosition < this.value.length) {
                e.preventDefault();

                let posInDecimal = cursorPosition - commaPos - 1;
                let beforeComma = this.value.substring(0, commaPos);
                let afterComma = this.value.substring(commaPos + 1);

                // Hapus karakter setelah cursor
                let newDecimal = afterComma.substring(0, posInDecimal) + afterComma.substring(posInDecimal + 1);

                // Rebuild - JANGAN hapus koma
                let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                let formatted = formatRupiah(newValue);
                this.value = formatted;

                let newCommaPos = this.value.indexOf(',');
                this.setSelectionRange(newCommaPos + posInDecimal + 1, newCommaPos + posInDecimal + 1);

                expenditureValue.value = parseRupiah(this.value);
            }
        }
    });

    // Format on blur to ensure proper format
    expenditureDisplay.addEventListener('blur', function(e) {
        if (e.target.value) {
            // Jika tidak ada koma, tambahkan ,00
            if (!e.target.value.includes(',')) {
                e.target.value = e.target.value + ',00';
            } else {
                // Jika ada koma tapi desimal kurang dari 2 digit, pad dengan 0
                let parts = e.target.value.split(',');
                if (parts[1] !== undefined) {
                    if (parts[1].length === 0) {
                        // Jika cuma koma tanpa desimal, tambahkan 00
                        e.target.value = parts[0] + ',00';
                    } else if (parts[1].length < 2) {
                        // Pad dengan 0 di belakang
                        e.target.value = parts[0] + ',' + parts[1].padEnd(2, '0');
                    }
                }
            }
            expenditureValue.value = parseRupiah(e.target.value);
        } else {
            e.target.value = '';
            expenditureValue.value = '';
        }
    });

    // Allow only numbers and comma
    expenditureDisplay.addEventListener('keypress', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
            // Allow comma (only one)
            (e.key === ',' && !this.value.includes(',')) ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }

        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // Initialize format jika ada old value (setelah validasi error)
    if (expenditureDisplay.value) {
        let formatted = formatRupiah(expenditureDisplay.value);
        expenditureDisplay.value = formatted;
        expenditureValue.value = parseRupiah(formatted);
    }
});
</script>
@endpush
@endsection