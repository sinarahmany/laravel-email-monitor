<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Email Monitor')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons (shadcn/ui compatible) -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        border: "hsl(214.3 31.8% 91.4%)",
                        input: "hsl(214.3 31.8% 91.4%)",
                        ring: "hsl(222.2 84% 4.9%)",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(222.2 84% 4.9%)",
                        primary: {
                            DEFAULT: "hsl(222.2 47.4% 11.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        secondary: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        destructive: {
                            DEFAULT: "hsl(0 84.2% 60.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        muted: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(215.4 16.3% 46.9%)",
                        },
                        accent: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        popover: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                        card: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                    },
                    borderRadius: {
                        lg: "0.5rem",
                        md: "calc(0.5rem - 2px)",
                        sm: "calc(0.5rem - 4px)",
                    },
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-background">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="border-b bg-card">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                <i data-lucide="mail" class="w-5 h-5 text-primary-foreground"></i>
                            </div>
                            <h1 class="text-2xl font-bold text-foreground">Email Monitor</h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-muted-foreground">v1.0.0</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1 container mx-auto px-4 py-6">
            @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800" role="alert">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                <div class="flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t bg-card py-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-center text-sm text-muted-foreground">
                    <span>Built by <a href="https://sinarahmannejad.com" target="_blank" class="text-primary hover:underline">Sina</a></span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
