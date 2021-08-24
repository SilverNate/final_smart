<template>
        <div>
        <li class="list-group-item" :class="className"><slot></slot></li>
        <span id="time">{{ time }}</span>
        <small class="badge float-right alert-success">{{ user }}</small>
        </div>
</template>

<script>

export default({
    props:[
        'color',
        'user',
        'time',
    ],
    computed: {
        className(){
            return 'list-group-item-'+this.color;
        },
        badgeClass(){
            return 'badge-'+this.color;
        },
    },
    mounted() {
        console.log('component mounted');
    },
});

</script>
<style scoped>
#time{
    font-size: 10px;
    margin-top: 10px;
    color: black;
}
</style>
