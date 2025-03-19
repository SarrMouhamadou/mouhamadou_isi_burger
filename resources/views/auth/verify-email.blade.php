@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Vérification de l'Email</div>
                <div class="card-body">
                    <p>Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous vous avons envoyé.</p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-primary">Renvoyer l'email de vérification</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link">Se déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
