@extends('layouts.app')

@section('title', 'Контакты')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h2 class="mb-4">Контакты</h2>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Ваше имя</label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', auth()->user()?->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', auth()->user()?->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea id="message" name="message" rows="5"
                                  class="form-control @error('message') is-invalid @enderror"
                                  required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
