var root = new Vue({
    el: '#root',
    data: {
        page: 0,
        perPage: 0,
        countPages: 0,
        bans: {},
        isLoaded: false,
        nickname: null,
        type: 'bans'
    },
    mounted: function () {
        this.setCurrentPage(1)
    },
    methods: {
        date_format(date) {
            if(!date) {
                date = new Date();
            }
            return ('0' + date.getDate()).slice(-2) + '.' + ('0' + (date.getMonth() + 1)).slice(-2) + '.' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes()
        },
        setCurrentPage(page) {
            this.isLoaded = false;
            axios.get('api.php', {params: {do: 'bans', 'page': page, 'name': this.nickname, 'type': this.type}})
                .then((res) => {
                    this.$refs.paginationComponent.changePage(res.data.page, res.data.countPages);
                    if(this.page != res.data.page) {
                        this.page = res.data.page;
                    }
                    if(this.perPage != res.data.perPage) {
                        this.perPage = res.data.perPage;
                    }
                    this.countPages = res.data.countPages;
                    this.bans = res.data.rows;
                    this.isLoaded = true
                })
        },
        forNick(event) {
            let nick = event.target.value.replace(/[^\w]/g, '');
            event.target.value = nick;
            if(!this.isLoaded) {
                event.preventDefault();
                return;
            }
            if(this.nickname && !nick) {
                this.nickname = null;
                this.setCurrentPage(1);
                return
            } else if(nick.length < 3) {
                return
            }
            this.nickname = nick;
            this.setCurrentPage(1)
        },
        forType(type) {
            this.type = type;
            this.setCurrentPage(1)
        }
    },
    components: {
        'banlist-pagination': paginationComponent
    }
});