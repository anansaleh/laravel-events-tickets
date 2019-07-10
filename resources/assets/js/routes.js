import Home from './components/Home.vue';
import Events from './components/events/List.vue';
import Tickets from './components/tickets/List.vue';
export const routes =[
    {
        path:'/',
        // redirect: '/events',
        component: Home
    },
    {
        path:'/events'
        ,component: Events
    }
    , {
        path: '/events/:event_id/tickets',
        component: Tickets
    }


];