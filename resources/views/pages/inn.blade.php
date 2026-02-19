@extends('layouts.app')

@section('title', 'Реквизиты')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h2 class="mb-4">Реквизиты</h2>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 40%">Наименование</td>
                            <td>— (заполнить)</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">ИНН</td>
                            <td>— (заполнить)</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">ОГРН / ОГРНИП</td>
                            <td>— (заполнить)</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Email</td>
                            <td>— (заполнить)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
