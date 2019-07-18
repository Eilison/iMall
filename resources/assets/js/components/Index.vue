<template>
    <index-banner></index-banner>
    <index-topic></index-topic>
    <index-plate></index-plate>
    <div><index-commodity></index-commodity></div>
    <div style="margin-bottom: 8px;clear: both"></div>
</template>

<script>
    import IndexBanner from './Index/IndexBanner.vue'
    import IndexTopic from './Index/IndexTopic.vue'
    import IndexPlate from './Index/IndexPlate.vue'
    import IndexCommodity from './Index/IndexCommodity.vue'

    export default{
        components:{
            IndexBanner, IndexTopic, IndexPlate,IndexCommodity
        }
    }

</script>