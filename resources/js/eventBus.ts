import mitt from 'mitt';
type Events = {
  'menu-updated': void
}

export const eventBus = mitt<Events>();