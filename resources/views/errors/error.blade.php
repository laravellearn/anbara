@if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)

            <li class="alert-dismissible d-flex align-items-center" role="alert">
                {{ $error }} <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </li>
        @endforeach
    </ul>
@endif