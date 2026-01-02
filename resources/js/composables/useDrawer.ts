import { ref, watch } from 'vue';

export interface UseDrawerParams {
    initialVisible?: boolean;
    blockScrollOnOpen?: boolean;
}

/**
 * Composable to manage a drawer's visibility state. It provides methods to open and close the drawer,
 * and optionally blocks background scrolling when the drawer is open.
 */
export function useDrawer(
    params: UseDrawerParams = {
        initialVisible: false,
        blockScrollOnOpen: false,
    },
) {
    const visible = ref(params.initialVisible ?? false);
    let scrollPosition = 0;

    function open() {
        visible.value = true;
    }

    function close() {
        visible.value = false;
    }

    watch(visible, (isVisible) => {
        if (isVisible && params.blockScrollOnOpen) {
            // Save current scroll position
            scrollPosition = window.scrollY;

            // Block scroll for iOS and other browsers
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollPosition}px`;
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
        } else {
            // Restore scroll position
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflow = '';

            // Restore scroll position
            window.scrollTo(0, scrollPosition);
        }
    });

    return {
        visible,
        open,
        close,
    };
}
