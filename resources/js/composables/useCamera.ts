import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { ref } from 'vue';

export interface Photo {
    filepath: string;
    webviewPath?: string;
    blob?: Blob;
}

export function useCamera() {
    const photos = ref<Photo[]>([]);

    async function takePhoto() {
        try {
            const capturedPhoto = await Camera.getPhoto({
                resultType: CameraResultType.Uri,
                source: CameraSource.Prompt, // Let user choose between camera or gallery
                quality: 100,
                allowEditing: true,
                promptLabelHeader: 'Adicionar Foto',
                promptLabelCancel: 'Cancelar',
                promptLabelPhoto:  'Escolher da Galeria',
                promptLabelPicture: 'Usar Câmera',
            });

            const fileName = Date.now() + '.jpeg';

            // Convert to blob for file upload
            let blob: Blob | undefined;
            if (capturedPhoto.webPath) {
                const response = await fetch(capturedPhoto.webPath);
                blob = await response.blob();
            }

            const savedImageFile = {
                filepath: fileName,
                webviewPath: capturedPhoto.webPath,
                blob,
            };

            photos.value = [savedImageFile, ...photos.value];
            return savedImageFile;
        } catch (error) {
            console.error('Error capturing photo:', error);
        }
    }

    function removePhoto(photo: Photo) {
        photos.value = photos.value.filter(p => p !== photo);
    }

    function clearPhotos() {
        photos.value = [];
    }

    return {
        takePhoto,
        removePhoto,
        clearPhotos,
        photos,
    };
}
