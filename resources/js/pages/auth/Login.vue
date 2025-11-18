<script setup>
defineOptions({
  layout: null
})
</script>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    login: null,
    password: null,
});

function submit() {
    form.post("/login", {
        onSuccess: () => {
            // Успешный вход
            form.reset();
        },
        onError: (errors) => {
            console.log("Login errors:", errors);
        },
        preserveState: true,
        preserveScroll: true,
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
            <h2 class="text-center mb-4 text-primary">Вход</h2>

            <!-- Общие ошибки -->
            <div v-if="form.hasErrors" class="alert alert-danger">
                <div v-for="error in form.errors" :key="error">
                    {{ error }}
                </div>
            </div>

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
                    placeholder="Введите ваш логин"
                />
                <div v-if="form.errors.login" class="invalid-feedback">
                    {{ form.errors.login }}
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold"
                    >Введите пароль:</label
                >
                <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Введите ваш пароль"
                />
                <div v-if="form.errors.password" class="invalid-feedback">
                    {{ form.errors.password }}
                </div>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-100 py-2 mb-3 fw-semibold"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Вход...</span>
                <span v-else>Войти</span>
            </button>

            <div class="text-center">
                <Link
                    href="/registration"
                    class="text-decoration-none text-primary"
                >
                    Нет аккаунта? Создайте его.
                </Link>
            </div>
        </form>
    </main>
</template>
