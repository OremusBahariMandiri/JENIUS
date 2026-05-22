@extends('layouts.app')

@section('title', 'Add Contract')

@section('content')
<div class="container-fluid contractPage">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header text-black d-flex justify-content-between align-items-center" style="background-color: #d1fae5">
                    <span class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Contract</span>
                    <a href="{{ route('contract.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('contract.store') }}" method="POST" id="contractForm">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Contract No. <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   name="no_contract"
                                                   class="form-control @error('no_contract') is-invalid @enderror"
                                                   value="{{ old('no_contract') }}"
                                                   required>
                                            @error('no_contract')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Contract Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   name="contract"
                                                   class="form-control @error('contract') is-invalid @enderror"
                                                   value="{{ old('contract') }}"
                                                   required>
                                            @error('contract')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Customer <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_md_cust"
                                            class="form-select @error('id_md_cust') is-invalid @enderror"
                                            required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id_md_cust }}"
                                                {{ old('id_md_cust') == $customer->id_md_cust ? 'selected' : '' }}>
                                            {{ $customer->customer }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_md_cust')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Expenditure Value <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">IDR</span>
                                        <input type="text"
                                               id="expenditure_display"
                                               class="form-control @error('expenditure') is-invalid @enderror"
                                               value="{{ old('expenditure') ? number_format(old('expenditure'), 2, ',', '.') : '' }}"
                                               required>
                                        <input type="hidden" name="expenditure" id="expenditure_value" value="{{ old('expenditure') }}">
                                        @error('expenditure')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Start Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                   name="date_start"
                                                   class="form-control @error('date_start') is-invalid @enderror"
                                                   value="{{ old('date_start') }}"
                                                   required>
                                            @error('date_start')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                End Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                   name="date_end"
                                                   class="form-control @error('date_end') is-invalid @enderror"
                                                   value="{{ old('date_end') }}"
                                                   required>
                                            @error('date_end')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Note</label>
                                    <textarea name="note"
                                              class="form-control @error('note') is-invalid @enderror"
                                              rows="3">{{ old('note') }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('contract.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .contractPage .card {
        border: none;
        border-radius: 10px;
    }

    .contractPage .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .contractPage .form-control:focus,
    .contractPage .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
    }

    .contractPage .form-label {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .contractPage .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .contractPage .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .contractPage .btn-success {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .contractPage .btn-success:hover {
        background-color: var(--dark-green);
        border-color: var(--dark-green);
    }

    .contractPage .input-group-text {
        background-color: var(--light-green);
        border-color: var(--primary-green);
        color: var(--dark-green);
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const expenditureDisplay = document.getElementById('expenditure_display');
    const expenditureValue = document.getElementById('expenditure_value');

    function formatRupiah(value) {
        let number = value.replace(/[^\d,]/g, '');
        number = number.replace(/\./g, '');
        if (number === '') return '';

        let parts = number.split(',');
        let integerPart = parts[0];
        let decimalPart = parts.length > 1 ? parts[1] : '';

        if (decimalPart.length > 2) {
            decimalPart = decimalPart.substring(0, 2);
        }

        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        if (parts.length > 1) {
            return integerPart + ',' + decimalPart;
        } else {
            return integerPart + ',00';
        }
    }

    function parseRupiah(value) {
        if (value === '') return '';
        let cleaned = value.replace(/\./g, '').replace(',', '.');
        return cleaned;
    }

    expenditureDisplay.addEventListener('input', function(e) {
        let cursorPosition = this.selectionStart;
        let beforeCursor = this.value.substring(0, cursorPosition);
        let formatted = formatRupiah(this.value);
        this.value = formatted;

        if (!beforeCursor.includes(',')) {
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
            let commaPos = this.value.indexOf(',');
            let decimalDigitsInput = beforeCursor.split(',')[1] || '';
            let decimalDigits = decimalDigitsInput.length;
            let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
            this.setSelectionRange(newPos, newPos);
        }

        expenditureValue.value = parseRupiah(formatted);
    });

    expenditureDisplay.addEventListener('keydown', function(e) {
        let cursorPosition = this.selectionStart;
        let selectionEnd = this.selectionEnd;
        let commaPos = this.value.indexOf(',');

        if (e.key === 'Backspace') {
            if (cursorPosition !== selectionEnd) {
                return;
            }

            if (commaPos !== -1 && cursorPosition > commaPos + 1) {
                e.preventDefault();
                let posInDecimal = cursorPosition - commaPos - 1;
                let beforeComma = this.value.substring(0, commaPos);
                let afterComma = this.value.substring(commaPos + 1);
                let newDecimal = afterComma.substring(0, posInDecimal - 1) + afterComma.substring(posInDecimal);
                let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                let formatted = formatRupiah(newValue);
                this.value = formatted;
                let newCommaPos = this.value.indexOf(',');
                let newCursorPos = newCommaPos + Math.max(1, posInDecimal);
                this.setSelectionRange(newCursorPos, newCursorPos);
                expenditureValue.value = parseRupiah(this.value);
            } else if (commaPos !== -1 && cursorPosition === commaPos + 1) {
                e.preventDefault();
            } else if (cursorPosition === commaPos) {
                e.preventDefault();
                let beforeComma = this.value.substring(0, commaPos);
                this.value = beforeComma;
                this.setSelectionRange(beforeComma.length, beforeComma.length);
                expenditureValue.value = parseRupiah(this.value);
            }
        } else if (e.key === 'Delete') {
            if (cursorPosition !== selectionEnd) {
                return;
            }

            if (cursorPosition === commaPos) {
                e.preventDefault();
                let beforeComma = this.value.substring(0, commaPos);
                this.value = beforeComma;
                this.setSelectionRange(beforeComma.length, beforeComma.length);
                expenditureValue.value = parseRupiah(this.value);
                return;
            }

            if (commaPos !== -1 && cursorPosition > commaPos && cursorPosition < this.value.length) {
                e.preventDefault();
                let posInDecimal = cursorPosition - commaPos - 1;
                let beforeComma = this.value.substring(0, commaPos);
                let afterComma = this.value.substring(commaPos + 1);
                let newDecimal = afterComma.substring(0, posInDecimal) + afterComma.substring(posInDecimal + 1);
                let newValue = beforeComma.replace(/\./g, '') + ',' + newDecimal;
                let formatted = formatRupiah(newValue);
                this.value = formatted;
                let newCommaPos = this.value.indexOf(',');
                this.setSelectionRange(newCommaPos + posInDecimal + 1, newCommaPos + posInDecimal + 1);
                expenditureValue.value = parseRupiah(this.value);
            }
        }
    });

    expenditureDisplay.addEventListener('blur', function(e) {
        if (e.target.value) {
            if (!e.target.value.includes(',')) {
                e.target.value = e.target.value + ',00';
            } else {
                let parts = e.target.value.split(',');
                if (parts[1] !== undefined) {
                    if (parts[1].length === 0) {
                        e.target.value = parts[0] + ',00';
                    } else if (parts[1].length < 2) {
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

    expenditureDisplay.addEventListener('keypress', function(e) {
        if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
            (e.key === ',' && !this.value.includes(',')) ||
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    if (expenditureDisplay.value) {
        let formatted = formatRupiah(expenditureDisplay.value);
        expenditureDisplay.value = formatted;
        expenditureValue.value = parseRupiah(formatted);
    }

    // Auto-focus on contract no
    $('input[name="no_contract"]').focus();
});
</script>
@endpush