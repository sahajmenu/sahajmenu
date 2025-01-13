@vite('resources/css/app.css')
<div class="bg-gray-200 rounded-xl md:w-1/2 p-4">
    <div class="space-y-4">
        <h1 class="font-black text-2xl">{{ $getRecord()->client->name }}</h1>
        <div class="flex gap-x-4">
            <div class="hidden md:flex flex-col justify-between">
                <div class="text-justify font-light">
                    <p>Scan the QR Code </p>
                    <p>with your smart </p>
                    <p>phone camera to </p>
                    <p>view our menu</p>
                </div>
                <p class="font-black text-xl">{{ $getRecord()->name }}</p>
            </div>
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->margin(2)->generate($getRecord()->table_link)) !!} " alt="table-link">
        </div>
    </div>
</div>

