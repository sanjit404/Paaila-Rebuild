<style>
    .brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.brand img {
    display: block;
}

.brand-sm img { width: 30px; }
.brand-md img { width: 45px; }
.brand-lg img { width: 150px; box-shadow: 0px 0px 10px green }


</style>
<a href="{{ url('/') }}" class="brand brand-{{ $size ?? 'md' }}">
    <img src="{{ asset('images/paailaLogo.png') }}" alt="Paaila Logo">
</a>