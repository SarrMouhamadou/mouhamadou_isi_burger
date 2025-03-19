@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <a href="{{ route('burgers.create') }}" class="add-link">Ajouter un Burger</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Image</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($burgers as $burger)
                <tr>
                    <td>{{ $burger->name }}</td>
                    <td>
                        @if ($burger->image)
                            <img src="{{ Storage::url($burger->image) }}" alt="{{ $burger->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <span class="text-muted">Pas d'image</span>
                        @endif
                    </td>
                    <td>{{ number_format($burger->price, 0, ',', '.') }} FCFA</td>
                    <td>{{ $burger->stock }}</td>
                    <td>
                        @if ($burger->isAvailable())
                            <span class="badge bg-success">Disponible</span>
                        @else
                            <span class="badge bg-danger">Indisponible</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('burgers.edit', $burger) }}" class="btn btn-action btn-sm">Modifier</a>
                        @if (!$burger->archived)
                            <form action="{{ route('burgers.archive', $burger) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-action btn-sm" onclick="return confirm('Voulez-vous archiver ce burger ?')">Archiver</button>
                            </form>
                        @endif
                        <form action="{{ route('burgers.destroy', $burger) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-sm" onclick="return confirm('Voulez-vous supprimer ce burger ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
