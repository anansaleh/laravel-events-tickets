<template>

    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-end">
            <li class="page-item" :class="{'disabled': isOnFirstPage}">
                <a class="page-link" href="" @click.prevent="loadPage(tablePagination.current_page - 1)">
                    <span>&laquo;</span>
                </a>
            </li>

            <template v-if="notEnoughPages">
                <li class="page-item" v-for="n in totalPage" :class="{'active': isCurrentPage(n)}">
                    <a class="page-link" href="" @click.prevent="loadPage(n)" v-html="n"></a>
                </li>
            </template>
            <template v-else>
                <li class="page-item" v-for="n in windowSize" :class="{'active': isCurrentPage(windowStart+n-1)}">
                    <a class="page-link" href="" @click.prevent="loadPage(windowStart+n-1)" v-html="windowStart+n-1"></a>
                </li>
            </template>

            <li class="page-item" :class="{'disabled': isOnLastPage}">
                <a class="page-link" href="" @click.prevent="loadPage(tablePagination.current_page + 1)">
                    <span>&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

</template>

<script>
    import VuetablePaginationMixin from 'vuetable-2/src/components/VuetablePaginationMixin'
    export default {
        mixins: [VuetablePaginationMixin]
    }

</script>
