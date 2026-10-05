<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";

type Book = {
    id: number;
    title: string;
    category_id: number;
    publisher_id: number;
    book_type: "new" | "old";
    cover_image_url: string | null;
    editor_name: string | null;
    author_name: string | null;
    written_by: string | null;
    publish_date: string | null;
    edition: string | null;
    price: string | null;
    purchased_date: string | null;
    regd_date: string | null;
    remarks: string | null;
    status: number;
};
const props = defineProps<{
    book?: Book;
    categories: { id: number; title: string }[];
    publishers: { id: number; title: string }[];
}>();
const dateValue = (value?: string | null) => value?.slice(0, 10) ?? "";
const form = useForm({
    title: props.book?.title ?? "",
    category_id: props.book?.category_id ?? "",
    publisher_id: props.book?.publisher_id ?? "",
    book_type: props.book?.book_type ?? "new",
    cover_image: null as File | null,
    editor_name: props.book?.editor_name ?? "",
    author_name: props.book?.author_name ?? "",
    written_by: props.book?.written_by ?? "",
    publish_date: dateValue(props.book?.publish_date),
    edition: props.book?.edition ?? "",
    price: props.book?.price ?? "",
    purchased_date: dateValue(props.book?.purchased_date),
    regd_date: dateValue(props.book?.regd_date),
    remarks: props.book?.remarks ?? "",
    status: props.book?.status ?? 1,
});
const submit = () =>
    props.book ? form.put(`/books/${props.book.id}`) : form.post("/books");

const coverPreview = ref<string | null>(props.book?.cover_image_url ?? null);
const onCoverChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.cover_image = file;
    coverPreview.value = file
        ? URL.createObjectURL(file)
        : (props.book?.cover_image_url ?? null);
};
</script>

<template>
    <Head :title="book ? $t('Edit book') : $t('New book')" />
    <main class="space-y-6 p-4 md:p-6">
        <header>
            <Link
                href="/books"
                class="text-sm text-muted-foreground hover:text-foreground"
                >{{ $t("Books") }}</Link
            >
            <h1 class="mt-2 text-2xl font-semibold">
                {{ book ? $t("Edit book") : $t("Add book") }}
            </h1>
        </header>
        <form
            class="grid gap-5 sm:grid-cols-2 xl:max-w-5xl"
            @submit.prevent="submit"
        >
            <label class="grid gap-2 text-sm font-medium sm:col-span-2"
                >{{ $t("Title")
                }}<input
                    v-model="form.title"
                    required
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.title"
            /></label>
            <label class="grid gap-2 text-sm font-medium sm:col-span-2">
                {{ $t("Cover image") }}
                <img
                    v-if="coverPreview"
                    :src="coverPreview"
                    :alt="$t('Cover preview')"
                    class="h-40 w-28 rounded-md border object-cover"
                />
                <input
                    type="file"
                    accept="image/*"
                    class="text-sm font-normal"
                    @change="onCoverChange"
                /><InputError :message="form.errors.cover_image" />
            </label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Category")
                }}<select
                    v-model="form.category_id"
                    required
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option value="" disabled>
                        {{ $t("Select a category") }}
                    </option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.title }}
                    </option></select
                ><InputError :message="form.errors.category_id"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Publisher")
                }}<select
                    v-model="form.publisher_id"
                    required
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option value="" disabled>
                        {{ $t("Select a publisher") }}
                    </option>
                    <option
                        v-for="publisher in publishers"
                        :key="publisher.id"
                        :value="publisher.id"
                    >
                        {{ publisher.title }}
                    </option></select
                ><InputError :message="form.errors.publisher_id"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Book type")
                }}<select
                    v-model="form.book_type"
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option value="new">{{ $t("New / Fresh") }}</option>
                    <option value="old">{{ $t("Old") }}</option></select
                ><InputError :message="form.errors.book_type"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Author name")
                }}<input
                    v-model="form.author_name"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.author_name"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Written by")
                }}<input
                    v-model="form.written_by"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.written_by"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Editor name")
                }}<input
                    v-model="form.editor_name"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.editor_name"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Edition")
                }}<input
                    v-model="form.edition"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.edition"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Publish date")
                }}<input
                    v-model="form.publish_date"
                    type="date"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.publish_date"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Price")
                }}<input
                    v-model="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.price"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Purchased date")
                }}<input
                    v-model="form.purchased_date"
                    type="date"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.purchased_date"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Registration date")
                }}<input
                    v-model="form.regd_date"
                    type="date"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.regd_date"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Status")
                }}<select
                    v-model.number="form.status"
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option :value="1">{{ $t("Active") }}</option>
                    <option :value="0">{{ $t("Inactive") }}</option></select
                ><InputError :message="form.errors.status"
            /></label>
            <label class="grid gap-2 text-sm font-medium sm:col-span-2"
                >{{ $t("Remarks")
                }}<textarea
                    v-model="form.remarks"
                    rows="4"
                    class="rounded-md border bg-background px-3 py-2 font-normal"
                ></textarea
                ><InputError :message="form.errors.remarks"
            /></label>
            <div class="flex gap-3 sm:col-span-2">
                <Button
                    :disabled="
                        form.processing ||
                        categories.length === 0 ||
                        publishers.length === 0
                    "
                    >{{
                        form.processing ? $t("Saving…") : $t("Save book")
                    }}</Button
                ><Button as-child variant="outline"
                    ><Link href="/books">{{ $t("Cancel") }}</Link></Button
                >
                <p
                    v-if="categories.length === 0 || publishers.length === 0"
                    class="self-center text-sm text-destructive"
                >
                    {{
                        $t(
                            "Add a category and publisher before registering books.",
                        )
                    }}
                </p>
            </div>
        </form>
    </main>
</template>
