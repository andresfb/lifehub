@props([
    'formId',
    'viewport',
    'formClass',
    'sectionWrapperClass' => '',
    'titleWrapperClass' => '',
    'urlWrapperClass' => '',
    'descriptionWrapperClass' => '',
    'editGroupClass' => '',
    'tagsWrapperClass' => '',
    'titleInputRef' => null,
    'tagKeyPrefix',
])

<form id="{{ $formId }}" method="POST" x-bind:action="form.action" class="{{ $formClass }}">
    @csrf

    <template x-if="form.method !== 'POST'">
        <input type="hidden" name="_method" x-bind:value="form.method"/>
    </template>

    <div class="{{ $sectionWrapperClass }}">
        <label for="pin-section-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Section</span>
        </label>
        <select id="pin-section-{{ $viewport }}"
                name="section_slug"
                x-model="form.sectionSlug"
                x-on:change="syncSectionName()"
                class="select select-bordered w-full"
        >
            <template x-for="section in sections" :key="section.slug">
                <option :value="section.slug" x-text="section.name"></option>
            </template>
        </select>
    </div>

    <div class="{{ $titleWrapperClass }}">
        <label for="pin-title-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Title</span>
        </label>
        <input id="pin-title-{{ $viewport }}"
               name="title"
               value="{{ old('title') }}"
               @if($titleInputRef) x-ref="{{ $titleInputRef }}" @endif
               x-model="form.title"
               type="text"
               class="input input-bordered w-full"
               required
               minlength="3"
               maxlength="255"/>
    </div>

    <div class="{{ $urlWrapperClass }}">
        <label for="pin-url-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">URL</span>
        </label>
        <input id="pin-url-{{ $viewport }}"
               name="url"
               value="{{ old('url') }}"
               x-model="form.url"
               type="url"
               class="input input-bordered w-full"/>
    </div>

    <div>
        <label for="pin-icon-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Icon</span>
        </label>
        <input id="pin-icon-{{ $viewport }}"
               name="icon"
               value="{{ old('icon') }}"
               x-model="form.icon"
               type="text"
               class="input input-bordered w-full"
               maxlength="10"/>
    </div>

    <div>
        <label for="pin-icon-color-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Icon Color</span>
        </label>
        <input id="pin-icon-color-{{ $viewport }}"
               name="icon_color"
               value="{{ old('icon_color') }}"
               x-model="form.iconColor"
               type="text"
               class="input input-bordered w-full"
               maxlength="20"/>
    </div>

    <div class="{{ $descriptionWrapperClass }}">
        <label for="pin-description-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Description</span>
        </label>
        <textarea id="pin-description-{{ $viewport }}"
                  name="description"
                  x-model="form.description"
                  rows="4"
                  class="textarea textarea-bordered w-full"
        >{{ old('description') }}</textarea>
    </div>

    <div class="{{ $editGroupClass }}" x-show="mode === 'edit'">
        <div>
            <label for="pin-active-{{ $viewport }}" class="label px-0">
                <span class="label-text font-medium">Active</span>
            </label>
            <label for="pin-active-{{ $viewport }}" @class([
                'flex cursor-pointer items-center justify-between px-1 py-2',
                'min-h-12' => $viewport === 'mobile',
            ])>
                <input type="hidden" name="active" value="0"/>
                <input id="pin-active-{{ $viewport }}"
                       name="active"
                       value="1"
                       x-model="form.active"
                       type="checkbox"
                       class="toggle toggle-success" />
            </label>
        </div>

        <div>
            <label for="pin-order-{{ $viewport }}" class="label px-0">
                <span class="label-text font-medium">Order</span>
            </label>
            <input id="pin-order-{{ $viewport }}"
                   name="order"
                   value="{{ old('order', 1) }}"
                   x-model="form.order"
                   type="number"
                   min="1"
                   class="input input-bordered w-full"/>
        </div>
    </div>

    <div class="{{ $tagsWrapperClass }}">
        <label for="pin-tags-{{ $viewport }}" class="label px-0">
            <span class="label-text font-medium">Tags</span>
        </label>
        <input id="pin-tags-{{ $viewport }}"
               x-model="form.tagsText"
               type="text"
               class="input input-bordered w-full"/>
        <template x-for="(tag, index) in parsedTags" :key="`{{ $tagKeyPrefix }}-${index}-${tag}`">
            <input type="hidden" name="tags[]" x-bind:value="tag"/>
        </template>
    </div>

</form>
