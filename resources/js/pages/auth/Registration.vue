<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    login: null,
    password: null,
    password_confirmation: null,
});

function submit() {
    // Добавляем обработку ответа и ошибок
    form.post("/registration", {
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
    <main
        class="min-vh-100 d-flex align-items-center justify-content-center bg-light"
    >
        <form
            @submit.prevent="submit"
            class="bg-white p-5 rounded shadow-sm border w-100"
            style="max-width: 400px"
        >
            <h2 class="text-center mb-4 text-primary">Регистрация</h2>

            <div class="mb-3">
                <label for="login" class="form-label fw-semibold"
                    >Введите логин:</label
                >
                <input
                    type="text"
                    id="login"
                    v-model="form.login"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.login }"
                    placeholder="Придумайте логин"
                />
                <div v-if="form.errors.login" class="invalid-feedback">
                    {{ form.errors.login }}
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold"
                    >Введите пароль:</label
                >
                <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Введите пароль"
                />
                <div v-if="form.errors.password" class="invalid-feedback">
                    {{ form.errors.password }}
                </div>
            </div>

            <div class="mb-4">
                <label
                    for="password_confirmation"
                    class="form-label fw-semibold"
                    >Подтвердите пароль:</label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                    placeholder="Повторите пароль"
                />
                <div
                    v-if="form.errors.password_confirmation"
                    class="invalid-feedback"
                >
                    {{ form.errors.password_confirmation }}
                </div>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-100 py-2 mb-3 fw-semibold"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Регистрация...</span>
                <span v-else>Зарегистрироваться</span>
            </button>

            <div class="text-center">
                <Link href="/login" class="text-decoration-none text-primary">
                    Уже есть аккаунт? Войдите в него.
                </Link>
            </div>
        </form>
    </main>
</template>
