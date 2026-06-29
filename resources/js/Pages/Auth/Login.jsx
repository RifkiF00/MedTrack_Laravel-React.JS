import { useEffect, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        username: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <div className="min-h-screen flex bg-white font-sans text-slate-800">
            <Head title="Log In - MedTrack IPSRS" />

            {/* Split Screen Layout */}
            <div className="w-full lg:w-1/2 flex flex-col justify-between p-8 sm:p-12 md:p-16 z-10 bg-white">
                {/* Top Section: Logo & Brand */}
                <div className="flex items-center space-x-3">
                    <img 
                        src="/uploads/assets/logo-rs.png" 
                        alt="Logo RS" 
                        className="h-10 w-auto bg-white p-1 rounded shadow-sm border border-slate-100"
                    />
                    <span className="font-extrabold text-sm tracking-widest text-[#0a3a60] uppercase">
                        MEDTRACK
                    </span>
                </div>

                {/* Center Section: Form */}
                <div className="max-w-md w-full mx-auto my-auto space-y-6">
                    <div className="space-y-1">
                        <h2 className="text-3xl font-extrabold tracking-tight text-slate-900">
                            Welcome Back!
                        </h2>
                        <p className="text-slate-400 text-sm">
                            Please enter log in details below
                        </p>
                    </div>

                    {status && <div className="text-sm font-semibold text-emerald-600 text-center">{status}</div>}

                    {/* Form Validation Errors */}
                    {errors.username && (
                        <div className="p-3 bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold rounded-2xl text-center">
                            ⚠️ {errors.username}
                        </div>
                    )}
                    {errors.password && !errors.username && (
                        <div className="p-3 bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold rounded-2xl text-center">
                            ⚠️ {errors.password}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-5">
                        {/* Username Field */}
                        <div className="space-y-1.5">
                            <label htmlFor="username" className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Username
                            </label>
                            <input
                                id="username"
                                type="text"
                                value={data.username}
                                onChange={(e) => setData('username', e.target.value)}
                                placeholder="Masukkan username"
                                className="w-full border-0 bg-slate-50 hover:bg-slate-100/80 focus:bg-slate-50 focus:ring-2 focus:ring-[#0a3a60] rounded-2xl px-4 py-3.5 text-sm transition duration-150 text-slate-800 placeholder-slate-400"
                                autoComplete="username"
                                required
                            />
                        </div>

                        {/* Password Field */}
                        <div className="space-y-1.5">
                            <label htmlFor="password" className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Password
                            </label>
                            <div className="relative">
                                <input
                                    id="password"
                                    type={showPassword ? 'text' : 'password'}
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="••••••"
                                    className="w-full border-0 bg-slate-50 hover:bg-slate-100/80 focus:bg-slate-50 focus:ring-2 focus:ring-[#0a3a60] rounded-2xl px-4 py-3.5 pr-12 text-sm transition duration-150 text-slate-800 placeholder-slate-300"
                                    autoComplete="current-password"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition"
                                >
                                    {showPassword ? '👁️' : '👁️‍🗨️'}
                                </button>
                            </div>
                        </div>

                        {/* Remember me & Forgot Password */}
                        <div className="flex justify-between items-center text-xs">
                            <label className="flex items-center cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-4 h-4 rounded border-slate-300 text-[#0a3a60] focus:ring-[#0a3a60]"
                                />
                                <span className="ml-2 text-slate-600 font-semibold">Remember me</span>
                            </label>
                            <a href="#" className="text-[#0a3a60] hover:underline font-bold">
                                Forgot password?
                            </a>
                        </div>

                        {/* Log In Button */}
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-3.5 bg-[#0a3a60] hover:bg-[#0c4a7a] text-white font-bold rounded-2xl shadow-md hover:shadow-lg active:scale-[0.99] transition duration-200 text-center text-sm"
                        >
                            {processing ? 'Logging in...' : 'Log in'}
                        </button>
                    </form>

                    {/* Decorative split dividers & social buttons (matching user screenshot) */}
                    <div className="space-y-4 pt-2">
                        <div className="flex items-center justify-between text-xs text-slate-400">
                            <span className="w-full border-t border-slate-100"></span>
                            <span className="px-3 uppercase font-bold text-[9px] tracking-wider shrink-0 select-none">or</span>
                            <span className="w-full border-t border-slate-100"></span>
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                disabled
                                className="flex items-center justify-center space-x-2 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 cursor-not-allowed select-none bg-slate-50/50"
                            >
                                <span>🌐</span> <span>Google</span>
                            </button>
                            <button
                                type="button"
                                disabled
                                className="flex items-center justify-center space-x-2 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-400 cursor-not-allowed select-none bg-slate-50/50"
                            >
                                <span>👤</span> <span>Facebook</span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* Bottom Section: Footer Links */}
                <div className="text-center text-xs text-slate-400 pt-8">
                    Don't have an account? <span className="text-[#0a3a60] font-bold cursor-not-allowed select-none">Sign Up</span>
                </div>
            </div>

            {/* Split Screen Panel: Illustration with Hospital Image & Wave */}
            <div className="hidden lg:block lg:w-1/2 relative bg-[#0a3a60] overflow-hidden">
                {/* Background image: bg-rs.jpeg */}
                <div 
                    className="absolute inset-0 bg-cover bg-center z-0" 
                    style={{ backgroundImage: "url('/uploads/assets/bg-rs.jpeg')" }}
                />
                {/* Dark teal gradient overlay */}
                <div className="absolute inset-0 bg-gradient-to-br from-[#0a3a60]/95 via-[#082a46]/90 to-[#0e4e75]/85 backdrop-blur-xs z-10" />

                {/* Mockup components layout */}
                <div className="absolute inset-0 z-20 p-16 flex flex-col justify-between">
                    <div className="max-w-md space-y-4">
                        <h2 className="text-white text-3xl font-extrabold tracking-tight uppercase leading-tight">
                            Asset Tracking &amp; Maintenance
                        </h2>
                        <p className="text-sky-100 text-sm leading-relaxed font-light">
                            Kelola mutasi, jadwal kalibrasi, pemeliharaan rutin, dan troubleshoot kerusakan alat medis Rumah Sakit secara terpadu.
                        </p>
                    </div>

                    {/* Graphic/Mockup card peeking from the bottom */}
                    <div className="w-full bg-white rounded-t-3xl shadow-2xl p-6 border-t border-l border-white/20 translate-y-24 rotate-1 transform origin-bottom-right transition duration-500 hover:translate-y-20">
                        <div className="flex items-center space-x-2 mb-4">
                            <span className="h-2 w-2 rounded-full bg-rose-500"></span>
                            <span className="h-2 w-2 rounded-full bg-amber-500"></span>
                            <span className="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span className="text-[10px] text-slate-400 font-semibold tracking-wider uppercase ml-2">SIMRS Medtrack Panel</span>
                        </div>
                        <div className="space-y-3.5">
                            <div className="h-3.5 bg-slate-100 rounded-full w-2/3"></div>
                            <div className="grid grid-cols-3 gap-2">
                                <div className="h-16 bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                                    <span className="h-2.5 bg-slate-200 rounded-full w-2/3"></span>
                                    <span className="h-4 bg-indigo-50 rounded-full w-1/2"></span>
                                </div>
                                <div className="h-16 bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                                    <span className="h-2.5 bg-slate-200 rounded-full w-2/3"></span>
                                    <span className="h-4 bg-teal-50 rounded-full w-1/2"></span>
                                </div>
                                <div className="h-16 bg-slate-50 border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                                    <span className="h-2.5 bg-slate-200 rounded-full w-2/3"></span>
                                    <span className="h-4 bg-rose-50 rounded-full w-1/2"></span>
                                </div>
                            </div>
                            <div className="h-24 bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col justify-between">
                                <span className="h-2.5 bg-slate-200 rounded-full w-1/3"></span>
                                <div className="h-12 w-full flex items-end justify-between px-2 pt-2">
                                    <div className="w-4 bg-indigo-200 h-6 rounded-t"></div>
                                    <div className="w-4 bg-indigo-300 h-10 rounded-t"></div>
                                    <div className="w-4 bg-indigo-400 h-14 rounded-t"></div>
                                    <div className="w-4 bg-indigo-500 h-8 rounded-t"></div>
                                    <div className="w-4 bg-[#0a3a60] h-16 rounded-t"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
