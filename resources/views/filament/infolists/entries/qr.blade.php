<div class="w-full md:w-[200px] space-y-2">
    <h1 class="font-black flex justify-center">{{ $getRecord()->client->name }}</h1>
    <div class="border-2 py-4 px-2 rounded-2xl">
        <div class="flex justify-center items-center">
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->margin(1)->generate($getRecord()->table_link)) !!} " alt="table-link">
        </div>
    </div>
    <h1 class="font-black flex justify-center">Table {{ $getRecord()->number }}</h1>
</div>

