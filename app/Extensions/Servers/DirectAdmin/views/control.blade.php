<div class="flex">
    <div class="flex-1">
        Package Name: {{$limit['package']}}</th>
        <br />
    </div>
    <div class="flex gap-2">
        <button class="button button-primary" onclick="directadmin_login()">
            Login
        </button>
        <button class="button button-primary" onclick="directadmin_reset_pwd()">
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
                    Bandwidth Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['bandwidth']}} MB / @if($limit['bandwidth'] == "unlimited") Unlimited @else
                    {{$limit['bandwidth']}} MB @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Disk Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['quota']}} MB / @if($limit['quota'] == "unlimited") Unlimited @else {{$limit['quota']}} MB
                    @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Main Doamin:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['domain']}}
                </td>
            </tr>

            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    Doamin Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['vdomains']}} Domains / @if($limit['vdomains'] == "unlimited") Unlimited @else
                    {{$limit['vdomains']}} Domains @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Mysql Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['mysql']}} Databases / @if($limit['mysql'] == "unlimited") Unlimited @else
                    {{$limit['mysql']}} Databases @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Panel Url:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['panel']}}
                </td>
            </tr>
            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    FTP Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['ftp']}} Accounts / @if($limit['ftp'] == "unlimited") Unlimited @else
                    {{$limit['ftp']}} Accounts @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Inode Usage:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$stats['inode']}} Inodes / @if($limit['inode'] == "unlimited") Unlimited @else
                    {{$limit['inode']}} Inodes @endif
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Username:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$user['username']}}
                </td>
            </tr>
            <tr class="">
                <th class="border border-gray-300 text-right px-4 py-2">
                    SSH Status:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$limit['ssh'] == "ON"?"Enabled":"Disabled"}}
                </td>
                <th class="border border-gray-300 text-right px-4 py-2">
                    Create At:
                </th>
                <td class="border border-gray-300 px-4 py-2">
                    {{$limit['date_created']}}
                </td>
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
    function directadmin_login() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("extensions.directadmin.login", $orderProduct->id) }}');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            console.log(xhr.responseText);
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if (data.status == 'success') {
                    // window.location.reload();
                    var win = window.open(data.data.url, '_blank');
                    win.focus();
                } else {
                    alert(data.message);
                }
            } else {
                alert('An error occurred while trying to perform this action.');
            }
        };
        xhr.onerror = function () {
            alert('An error occurred while trying to perform this action.');
        };
        xhr.send('_token={{ csrf_token() }}');
    }
    function directadmin_reset_pwd() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("extensions.directadmin.resetPwd", $orderProduct->id) }}');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            console.log(xhr.responseText);
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if (data.status == 'success') {
                    alert(data.message);
                    window.location.reload();
                    // var win = window.open(data.data.url, '_blank');
                    win.focus();
                } else {
                    alert(data.message);
                }
            } else {
                alert('An error occurred while trying to perform this action.');
            }
        };
        xhr.onerror = function () {
            alert('An error occurred while trying to perform this action.');
        };
        xhr.send('_token={{ csrf_token() }}');
    }
</script>