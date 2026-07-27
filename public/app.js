document.addEventListener('DOMContentLoaded', () => {
    const appNode = document.getElementById('app');

    if (appNode) {
        appNode.innerHTML = `
            <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-md">
                    <div>
                        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                            Create your account
                        </h2>
                        <p class="mt-2 text-center text-sm text-gray-600">
                            Join UniMarket campus community
                        </p>
                    </div>
                    <form id="registration-form" class="mt-8 space-y-6">
                        <div class="rounded-md shadow-sm space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                    class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                                    placeholder="student@university.edu">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="password" name="password" type="password" autocomplete="new-password" required 
                                    class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div>
                            <button type="submit" id="submit-btn" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
    }
});
