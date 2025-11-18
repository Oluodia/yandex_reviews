<script setup>
const props = defineProps({
    yandex_link: null,
    reviews: null,
    summary: null,
});
</script>

<template>
    <div v-if="reviews != null">
        <div class="row justify-content-between">
            <!-- Блок с отзывами слева -->
            <div class="col-lg-8 col-md-7">
                <div class="d-flex flex-column gap-4">
                    <div v-for="(review, index) in reviews" :key="index">
                        <div
                            class="bg-white p-4 rounded-4 shadow border border-tertiary"
                        >
                            <div
                                class="d-flex justify-content-between align-items-start mb-3"
                            >
                                <!-- Заголовок отзыва -->
                                <div>
                                    <div class="text-secondary small">
                                        {{ review["date"] }}
                                    </div>
                                    <div class="fw-bold text-dark fs-5">
                                        {{ review["author"] }}
                                    </div>
                                </div>

                                <div class="star-rating">
                                    <span
                                        v-for="star in 5"
                                        class="fs-4"
                                        :key="star"
                                        :class="[
                                            'star',
                                            star <= review.rating
                                                ? 'star-filled'
                                                : 'star-empty',
                                        ]"
                                    >
                                        ★
                                    </span>
                                </div>
                            </div>

                            <div class="text-dark lh-base">
                                {{ review["text"] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="sticky-top" style="top: 20px">
                    <div
                        class="bg-white p-4 rounded-4 shadow border border-tertiary text-center"
                    >
                        <div class="d-flex">
                            <div class="display-3 text-black mb-2">
                                {{ summary.average_rating?.toFixed(1) }}
                            </div>

                            <div
                                class="star-rating justify-content-center mb-3 fs-2"
                            >
                                <span
                                    v-for="star in 5"
                                    :key="star"
                                    :class="[
                                        'star',
                                        star <=
                                        Math.floor(summary?.average_rating || 0)
                                            ? 'star-filled'
                                            : 'star-empty',
                                    ]"
                                >
                                    ★
                                </span>
                            </div>
                        </div>

                        <hr class="text-secondary my-3" />

                        <div class="fw-bold text-start">
                            Всего отзывов:
                            {{ summary?.total_reviews?.toLocaleString() || 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="text-center py-5">
        <h1 class="text-muted">Нет данных</h1>
    </div>
</template>

<style scoped>
.star-rating {
    display: flex;
    gap: 2px;
    align-items: center;
}

.star-filled {
    color: #ffc107;
}

.star-empty {
    color: #e0e0e0;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .sticky-top {
        position: static !important;
    }
}
</style>
