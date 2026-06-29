import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import { useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function UpdateProfilePhotoForm({ className = '' }) {
    const user = usePage().props.auth.user;
    const { data, setData, post, processing, errors } = useForm({
        profile_photo: null,
    });
    const [preview, setPreview] = useState(user.profile_photo_url || null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        setData('profile_photo', file);

        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('profile.photo.update'), {
            forceFormData: true,
            onSuccess: () => {
                // Clear input after successful upload
                document.getElementById('profile_photo_input').value = '';
            }
        });
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900">Foto Profil</h2>
                <p className="mt-1 text-sm text-gray-600">
                    Unggah foto profil baru Anda. Format yang didukung: JPG, JPEG, PNG, atau WEBP (Maksimal 5MB).
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div className="flex items-center space-x-6">
                    <div className="w-20 h-20 rounded-full border-2 border-indigo-600 overflow-hidden bg-slate-100 flex items-center justify-center shadow-md shrink-0">
                        {preview ? (
                            <img src={preview} alt="Foto Profil" className="w-full h-full object-cover" />
                        ) : (
                            <span className="text-4xl text-slate-400">👤</span>
                        )}
                    </div>

                    <div className="flex-1 space-y-2">
                        <InputLabel htmlFor="profile_photo" value="Pilih Foto Baru" />
                        <input
                            id="profile_photo_input"
                            type="file"
                            accept="image/*"
                            onChange={handleFileChange}
                            className="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <InputError message={errors.profile_photo} className="mt-2" />
                    </div>
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing || !data.profile_photo}>
                        Unggah Foto
                    </PrimaryButton>
                </div>
            </form>
        </section>
    );
}
