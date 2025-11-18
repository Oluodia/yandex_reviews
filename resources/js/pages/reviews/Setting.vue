<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    link: null,
});

function submit() {
    // Добавляем обработку ответа и ошибок
    form.post("/settings", {
        onSuccess: () => {
            // Действия после успешной регистрации
            form.reset();
        },
        onError: (errors) => {
            console.log(errors);
        },
        preserveState: true,
    });
}
</script>

<template>
    <p class="fw-medium fs-5">Подключить Яндекс</p>
    <form @submit.prevent="submit()">
        <label for="link" class="form-label text-body-tertiary">
            <span class="fw-semibold"> Укажите ссылку на Яндекс, пример </span>
            <span class="text-decoration-underline">
                https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/
            </span>
        </label>
        <div v-if="form.hasErrors" class="alert alert-danger">
            <div v-for="error in form.errors" :key="error">
                {{ error }}
            </div>
        </div>
        <input
            type="text"
            id="link"
            v-model="form.link"
            class="form-control form-control-sm w-50 mb-4"
        />

        <button
            type="submit"
            class="btn btn-primary px-5 rounded-3 fw-semibold"
        >
            Сохранить
        </button>
    </form>
</template>
