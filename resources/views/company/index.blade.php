@foreach($companies as $company)
<section>
    {{$company->name}}
</section>
@endforeach



<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>