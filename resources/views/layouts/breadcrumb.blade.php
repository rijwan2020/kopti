<div class="row">
    <div class="col-md-6">
        <h4 class="font-weight-bold">
            @yield('module', 'Home')
            <div class="text-muted text-tiny mt-1">
                <small class="font-weight-normal">Today is {{date('l, d M Y')}}</small>
            </div>
        </h4>
    </div>
    <div class="col-md-6">
        <ol class="breadcrumb justify-content-end">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>
            @php
                $i = 0;
                $jml = count($data['breadcrumb']);
            @endphp
            @foreach ($data['breadcrumb'] as $key => $value)
                @php
                    $i++;
                @endphp
                <li class="breadcrumb-item {{$i == $jml?'active':''}}">
                    <a href="{{ $value }}">{{$key}}</a>
                </li>
            @endforeach
        </ol>
    </div>
</div>