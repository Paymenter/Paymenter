<div class="flex">
    <div class="flex-1">
        Package Name: {{$limit['package']}}</th>
        <br />
    </div>
    <div class="flex gap-2">
        <button class="button button-primary" onclick="">
            Login
        </button>
        <button class="button button-primary" onclick="">
            Reset Password
        </button>
    </div>
</div>
<!-- Show bars for currently used resources -->
<div class="flex">
    <table class="w-full border-collapse">
        <thead class="">
            <tr>
                <th scope="col" rowspan="6" class=""></th>
            </tr>
        </thead>
        <tbody>
            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    CDN Doamin:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['domain']}}
                </td>
            </tr>

            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    Panel Url:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['panel']}}
                </td>
            </tr>
            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    Username:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['username']}}
                </td>
            </tr>
            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    Password:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['password']}}
                </td>
            </tr>

        </tbody>
    </table>

</div>
<script>

</script>
