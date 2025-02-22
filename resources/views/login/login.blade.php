<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - MODN PRESSING</title>

    <!-- Importation correcte de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-4xl mx-auto p-6">
        <div class="flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Image (Masquée sur très petits écrans) -->
            <div class="hidden md:block md:w-1/2">
                <img class="object-cover w-full h-full dark:hidden" src="{{ asset('assets/img/login-office.jpeg') }}" alt="Office" />
                <img class="hidden object-cover w-full h-full dark:block" src="{{ asset('assets/img/login-office-dark.jpeg') }}" alt="Office" />
            </div>

            <!-- Formulaire -->
            <div class="w-full md:w-1/2 p-8 flex items-center justify-center">
                <div class="w-full max-w-sm">
                    <h1 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-6">Connexion</h1>

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-400 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login_verif') }}" method="POST">
                        @csrf

                        <label class="block text-sm mb-4">
                            <span class="text-gray-700 dark:text-gray-400">E-mail</span>
                            <input name="email" type="email" required
                                class="block w-full mt-1 px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-700
                                    focus:border-purple-400 focus:ring focus:ring-purple-300 focus:ring-opacity-50
                                    dark:text-gray-300 rounded-lg shadow-sm" 
                                placeholder="JaneDoe@modn.ci" />
                        </label>

                        <label class="block text-sm mb-4">
                            <span class="text-gray-700 dark:text-gray-400">Mot de passe</span>
                            <input name="password" type="password" required
                                class="block w-full mt-1 px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-700
                                    focus:border-purple-400 focus:ring focus:ring-purple-300 focus:ring-opacity-50
                                    dark:text-gray-300 rounded-lg shadow-sm" 
                                placeholder="***************" />
                        </label>

                        <button type="submit"
                            class="block w-full px-4 py-2 mt-4 text-sm font-medium text-white bg-purple-600 rounded-lg
                                hover:bg-purple-700 focus:outline-none focus:ring focus:ring-purple-300 focus:ring-opacity-50 transition">
                            Connexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
