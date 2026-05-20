@extends('layouts.app')

@section('title', 'Add Contract')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="h3 mb-2 text-gray-800">Add Contract</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom mb-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('contract.index') }}">Contract</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Add Contract Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contract.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Contract No. <span class="text-danger">*</span></label>
                                    <input type="text" name="no_contract"
                                        class="form-control @error('no_contract') is-invalid @enderror"
                                        value="{{ old('no_contract') }}" placeholder="e.g., CNT-2026-001" required>
                                    @error('no_contract')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contract Name <span class="text-danger">*</span></label>
                                    <input type="text" name="contract"
                                        class="form-control @error('contract') is-invalid @enderror"
                                        value="{{ old('contract') }}" placeholder="Contract name" required>
                                    @error('contract')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="id_md_cust" class="form-select @error('id_md_cust') is-invalid @enderror"
                                    required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id_md_cust }}"
                                            {{ old('id_md_cust') == $customer->id_md_cust ? 'selected' : '' }}>
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
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="expenditure_display"
                                        class="form-control @error('expenditure') is-invalid @enderror"
                                        value="{{ old('expenditure') ? number_format(old('expenditure'), 2, ',', '.') : '' }}"
                                        placeholder="0,00" required>
                                    <input type="hidden" name="expenditure" id="expenditure_value"
                                        value="{{ old('expenditure') }}">
                                    @error('expenditure')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date_start"
                                        class="form-control @error('date_start') is-invalid @enderror"
                                        value="{{ old('date_start') }}" required>
                                    @error('date_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date_end"
                                        class="form-control @error('date_end') is-invalid @enderror"
                                        value="{{ old('date_end') }}" required>
                                    @error('date_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3"
                                    placeholder="Additional notes">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save
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

            if (number === '') return '0,00';

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

            // Always show decimal with 2 digits
            if (parts.length > 1) {
                // User sudah mengetik koma
                return integerPart + ',' + decimalPart.padEnd(2, '0');
            } else {
                // User belum ketik koma, tampilkan ,00
                return integerPart + ',00';
            }
        }

        // Parse formatted Rupiah to number
        function parseRupiah(value) {
            let cleaned = value.replace(/\./g, '').replace(',', '.');
            return cleaned;
        }

        expenditureDisplay.addEventListener('input', function(e) {
            // Simpan posisi cursor dari belakang
            let valueLength = this.value.length;
            let cursorPosition = this.selectionStart;

            // Hitung posisi dari belakang, tapi jangan hitung ,00 jika cursor di tengah
            let beforeCursor = this.value.substring(0, cursorPosition);
            let afterCursor = this.value.substring(cursorPosition);

            // Format value
            let formatted = formatRupiah(this.value);
            this.value = formatted;

            // Cari posisi koma
            let commaIndex = formatted.indexOf(',');

            // Jika cursor sebelumnya sebelum koma atau tidak ada koma di input asli
            if (!beforeCursor.includes(',')) {
                // Set cursor sebelum koma
                let newCommaIndex = this.value.indexOf(',');
                let digitsBeforeCursor = beforeCursor.replace(/\D/g, '').length;
                let digitsBeforeComma = this.value.substring(0, newCommaIndex).replace(/\D/g, '').length;

                // Hitung posisi baru berdasarkan jumlah digit
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
                // Cursor setelah koma, set di posisi desimal
                let commaPos = this.value.indexOf(',');
                let decimalDigits = beforeCursor.split(',')[1]?.replace(/\D/g, '').length || 0;
                let newPos = commaPos + 1 + Math.min(decimalDigits, 2);
                this.setSelectionRange(newPos, newPos);
            }

            // Update hidden input
            expenditureValue.value = parseRupiah(formatted);
        });

        // Format on blur to ensure proper format
        expenditureDisplay.addEventListener('blur', function(e) {
            if (e.target.value && e.target.value !== '0,00') {
                let formatted = formatRupiah(e.target.value);
                e.target.value = formatted;
                expenditureValue.value = parseRupiah(formatted);
            } else {
                e.target.value = '0,00';
                expenditureValue.value = '0';
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

        // Initialize format jika ada old value
        if (expenditureDisplay.value) {
            let formatted = formatRupiah(expenditureDisplay.value);
            expenditureDisplay.value = formatted;
            expenditureValue.value = parseRupiah(formatted);
        } else {
            expenditureDisplay.value = '0,00';
            expenditureValue.value = '0';
        }
    });
    </script>
    @endpush
@endsection
