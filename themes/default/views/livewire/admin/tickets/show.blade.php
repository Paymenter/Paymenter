<div>
    <livewire:admin.tickets.messages :ticket="$ticket" />
<br>
<div class="overflow-hidden">
    <form id="reply"
        enctype="multipart/form-data">
        @csrf
        <div class="bg-white mb-3 border-gray-200 sm:px-20 dark:bg-secondary-100 dark:border-black mt-2">
            <h1 class="text-xl text-gray-500 dark:text-darkmodetext font-bold">{{ __('Reply') }}</h1>
            <div class="grid grid-cols-1 gap-4">
                <div class="mt-3 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">

                    <div id="attachments-list" class="flex flex-row gap-x-4 mb-3"></div>

                    @error('message')
                        <div class="text-red-500 text-xs mb-1">{{ $message }}</div>
                    @enderror
                    <div class="flex flex-row">
                        <label for="attachments"
                            class="button-secondary rounded-full cursor-pointer flex w-10 h-10 mr-2 transition-all ease-in-out">
                            <i class="ri-add-line my-auto mx-auto"></i>
                        </label>
                        <textarea id="message" wire:model="message"
                            class="block my-auto w-full rounded-2xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-indigo-300 dark:border-0 sm:text-sm dark:bg-secondary-200"
                            rows="1" name="message" placeholder="Aa"></textarea>
                        
                        <button type="button" id="submit-button"
                            class="button-primary rounded-full w-10 ml-2 h-10 float-right transition-all ease-in-out">
                            <i class="ri-send-plane-fill"></i>
                        </button>
                    </div>
                    <div class="bg-primary-400 h-[1px]" style="width: 0%;display:none" id="progress"></div>
                    <x-input type="file" id="attachments" :label="__('Attachments')" name="attachments[]" multiple
                        class="hidden" />
                    <br>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function slideDown() {
        const content = document.getElementById('content');
        const contentHeight = content.scrollHeight;
        content.scroll(0, contentHeight);
    }

    window.addEventListener('load', slideDown);

    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('attachments-list');
    const submitButton = document.getElementById('submit-button');

    const files = [];

    fileInput.addEventListener('change', (e) => {
        const selectedFiles = e.target.files;

        for (let i = 0; i < selectedFiles.length; i++) {
            files.push(selectedFiles[i]);
            const listItem = createListItem(selectedFiles[i]);

            fileList.appendChild(listItem);
        }

        fileInput.value = '';
    });

    function createListItem(file) {
        const listItem = document.createElement('div');
        listItem.classList.add('bg-secondary-200', 'rounded-md', 'w-full', 'max-w-[120px]', 'p-2', 'justify-center', 'flex-col', 'items-center', 'shadow-sm', 'mb-3');

        const fileContent = document.createElement('div');
        fileContent.classList.add('justify-center', 'flex');

        if (/\.(jpe?g|png|gif|bmp)$/i.test(file.name)) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            image.classList.add('max-h-10', 'rounded-sm');

            fileContent.appendChild(image);
        } else {
            const icon = document.createElement('i');
            icon.classList.add('ri-article-line', 'text-4xl', 'mx-auto', 'text-secondary-500');
            fileContent.appendChild(icon);
        }

        const fileName = document.createElement('div');
        fileName.textContent = file.name;
        fileName.classList.add('text-xs', 'text-center', 'text-secondary-500', 'w-full', 'truncate', 'mt-1');

        const removeButton = document.createElement('button');
        removeButton.textContent = 'x';
        removeButton.classList.add('text-red-500', 'hover:text-red-700', 'cursor-pointer', 'float-right', 'text-xs', 'font-bold');
        removeButton.addEventListener('click', () => {

            const index = files.indexOf(file);
            files.splice(index, 1);
            listItem.remove();
        });

        listItem.appendChild(removeButton);
        listItem.appendChild(fileContent);
        listItem.appendChild(fileName);

        return listItem;
    }

    submitButton.addEventListener('click', () => {
        if(files.length == 0) {
            @this.call('reply');
            return;
        }
        @this.uploadMultiple('attachments', files, (paths) => {
            @this.call('reply');
            // Reset the files array
            files.splice(0, files.length);
        });

        setTimeout(() => {
            slideDown();
        }, 500);
    });

    const tx = document.getElementsByTagName("textarea");
    for (let i = 0; i < tx.length; i++) {
        tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
        tx[i].addEventListener("input", OnInput, false);
    }

    function OnInput() {
        this.style.height = 0;
        this.style.height = (this.scrollHeight) + "px";
    }
</script>
</div>
