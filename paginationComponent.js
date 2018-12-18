var paginationComponent = {

    data: function () {
        return {
            pages: 0,
            page: 0
        }
    },

    template: '<ul class="pagination">' +
            '<li class="page-item"><span class="page-link" @click="$emit(\'set-page\', 1)"><<<</span></li>' +
            '<li :class="[\'page-item\', page == i ? \'active\' : \'\']" v-for="i in range()"><span class="page-link" @click="$emit(\'set-page\', i)">{{i}}</span></li>' +
            '<li class="page-item"><span class="page-link" @click="$emit(\'set-page\', pages)">>>></span></li>' +
        '</ul>',

    methods: {
        changePage(page, countPages) {
            this.page = page;
            this.pages = countPages
        },
        range : function () {
            if(this.pages < 3) {
                return this.pages;
            }

            let start = this.page > 3 ? this.page - 3 : 1;
            let end = (this.page + 3) > this.pages ? this.pages : (this.page + 3);
            return Array(end - start + 1).fill().map((_, idx) => start + idx)
        }
    }
};