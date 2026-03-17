@extends('layouts.reception')

@section('title', 'إضافة مريض للقائمة - Medicare')
@section('page-id', 'visit-create')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reception/views/visit-create.css') }}">
@endpush

@section('content')
<div class="visit-create-wrapper">
    <div class="vc-page-header">
        <h1 class="vc-title">إضافة مريض لقائمة الانتظار</h1>
        <p class="vc-subtitle">قم بتحديد الطبيب والأولوية لبدء زيارة المريض</p>
    </div>

    <form method="POST" action="{{ route('reception.visits.store', $patient) }}" class="vc-panel">
        @csrf
        
        <div class="vc-patient-info">
            <div class="vc-patient-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                    <path d="M4 21C4 17.6863 7.58172 15 12 15C16.4183 15 20 17.6863 20 21" fill="currentColor"/>
                </svg>
            </div>
            <div>
                <span class="vc-patient-name">{{ $patient->full_name }}</span>
                <span class="vc-patient-id">رقم الهوية: {{ $patient->national_id }}</span>
            </div>
        </div>

        <div class="vc-form-grid">
            <div class="vc-field vc-field--full">
                <label for="doctor_id">اختيار الطبيب</label>
                <select name="doctor_id" id="doctor_id" required>
                    <option value="">-- اختر الطبيب --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            د. {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')
                    <span style="color:red; font-size:12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="vc-field vc-field--full">
                <label for="notes">ملاحظات إضافية</label>
                <textarea name="notes" id="notes" rows="3" placeholder="أضف أي ملاحظات هامة هنا...">{{ old('notes') }}</textarea>
                @error('notes')
                    <span style="color:red; font-size:12px;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="vc-submit-row">
            <button type="submit" class="vc-btn vc-btn--primary">حفظ وإضافة للقائمة</button>
            <a href="{{ route('reception.patients.index') }}" class="vc-btn vc-btn--secondary">إلغاء والعودة</a>
        </div>
    </form>
</div>
@endsection
