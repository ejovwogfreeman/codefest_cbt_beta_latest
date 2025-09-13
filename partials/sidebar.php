<?php

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

?>



<aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <img src="img/logo2.png" alt="" width="120px" style="margin-left: 10px;">
        <button id="closeBtn" class="close-btn">&times;</button>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="https://exams.codefest.africa/dashboard?id=<?php echo $_SESSION['user']['user_id'] ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M3.76 16h12.48A7.998 7.998 0 0 0 10 3a7.998 7.998 0 0 0-6.24 13M10 4c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1M6 6c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m8 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m-5.37 5.55L12 7v6c0 1.1-.9 2-2 2s-2-.9-2-2c0-.57.24-1.08.63-1.45M4 10c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m12 0c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m-5 3c0-.55-.45-1-1-1s-1 .45-1 1s.45 1 1 1s1-.45 1-1" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/programs">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 32 32">
                    <path fill="currentColor" d="m16 7.78l-.313.095l-12.5 4.188L.345 13L2 13.53v8.75c-.597.347-1 .98-1 1.72a2 2 0 1 0 4 0c0-.74-.403-1.373-1-1.72v-8.06l2 .655V20c0 .82.5 1.5 1.094 1.97c.594.467 1.332.797 2.218 1.093c1.774.59 4.112.937 6.688.937s4.914-.346 6.688-.938c.886-.295 1.624-.625 2.218-1.093C25.5 21.5 26 20.82 26 20v-5.125l2.813-.938L31.655 13l-2.843-.938l-12.5-4.187zm0 2.095L25.375 13L16 16.125L6.625 13zm-8 5.688l7.688 2.562l.312.094l.313-.095L24 15.562V20c0 .01.004.126-.313.375c-.316.25-.883.565-1.625.813C20.58 21.681 18.395 22 16 22s-4.58-.318-6.063-.813c-.74-.247-1.308-.563-1.624-.812C7.995 20.125 8 20.01 8 20z" />
                </svg>
                <span>My Programs</span>
            </a>
        </li>
        <!-- <li>
            <a href="https://exams.codefest.africa/courses">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path d="M4 19V5a2 2 0 0 1 2-2h13.4a.6.6 0 0 1 .6.6v13.114M6 17h14M6 21h14" />
                        <path stroke-linejoin="round" d="M6 21a2 2 0 1 1 0-4" />
                        <path d="M9 7h6" />
                    </g>
                </svg>
                <span>Courses</span>
            </a>
        </li> -->
        <li>
            <a href="https://exams.codefest.africa/exams">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 256 256">
                    <path fill="currentColor" d="M216 42H40a14 14 0 0 0-14 14v160a6 6 0 0 0 8.68 5.37L64 206.71l29.32 14.66a6 6 0 0 0 5.36 0L128 206.71l29.32 14.66a6 6 0 0 0 5.36 0L192 206.71l29.32 14.66a6 6 0 0 0 2.68.63a5.93 5.93 0 0 0 3.15-.9A6 6 0 0 0 230 216V56a14 14 0 0 0-14-14m2 164.29l-23.32-11.66a6 6 0 0 0-5.36 0L160 209.29l-29.32-14.66a6 6 0 0 0-5.36 0L96 209.29l-29.32-14.66a6 6 0 0 0-5.36 0L38 206.29V56a2 2 0 0 1 2-2h176a2 2 0 0 1 2 2Zm-116.63-113a6 6 0 0 0-10.74 0l-32 64a6 6 0 1 0 10.74 5.36L75.71 150h40.58l6.34 12.68a6 6 0 1 0 10.74-5.36ZM81.71 138L96 109.42L110.29 138ZM198 128a6 6 0 0 1-6 6h-18v18a6 6 0 0 1-12 0v-18h-18a6 6 0 0 1 0-12h18v-18a6 6 0 0 1 12 0v18h18a6 6 0 0 1 6 6" />
                </svg>
                <span>My Exams</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/results">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 512 512">
                    <rect width="416" height="320" x="48" y="96" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" rx="56" ry="56" />
                    <path fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="60" d="M48 192h416M128 300h48v20h-48z" />
                </svg>
                <span>Check Results</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/certificates">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 256 256">
                    <path fill="currentColor" d="M128 136a8 8 0 0 1-8 8H72a8 8 0 0 1 0-16h48a8 8 0 0 1 8 8m-8-40H72a8 8 0 0 0 0 16h48a8 8 0 0 0 0-16m112 65.47V224a8 8 0 0 1-12 7l-24-13.74L172 231a8 8 0 0 1-12-7v-24H40a16 16 0 0 1-16-16V56a16 16 0 0 1 16-16h176a16 16 0 0 1 16 16v30.53a51.88 51.88 0 0 1 0 74.94M160 184v-22.53A52 52 0 0 1 216 76V56H40v128Zm56-12a51.88 51.88 0 0 1-40 0v38.22l16-9.16a8 8 0 0 1 7.94 0l16 9.16Zm16-48a36 36 0 1 0-36 36a36 36 0 0 0 36-36" />
                </svg>
                <span>My Certificates</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/profile?id=<?php echo $userId; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4s-4 1.79-4 4s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z" />
                </svg>
                <span>Profile</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/settings">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                    <path fill="currentColor" d="m9.25 22l-.4-3.2q-.325-.125-.612-.3t-.563-.375L4.7 19.375l-2.75-4.75l2.575-1.95Q4.5 12.5 4.5 12.338v-.675q0-.163.025-.338L1.95 9.375l2.75-4.75l2.975 1.25q.275-.2.575-.375t.6-.3l.4-3.2h5.5l.4 3.2q.325.125.613.3t.562.375l2.975-1.25l2.75 4.75l-2.575 1.95q.025.175.025.338v.674q0 .163-.05.338l2.575 1.95l-2.75 4.75l-2.95-1.25q-.275.2-.575.375t-.6.3l-.4 3.2zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.488 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12" />
                </svg>
                <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="https://exams.codefest.africa/logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M14.945 1.25c-1.367 0-2.47 0-3.337.117c-.9.12-1.658.38-2.26.981c-.524.525-.79 1.17-.929 1.928c-.135.737-.161 1.638-.167 2.72a.75.75 0 0 0 1.5.008c.006-1.093.034-1.868.142-2.457c.105-.566.272-.895.515-1.138c.277-.277.666-.457 1.4-.556c.755-.101 1.756-.103 3.191-.103h1c1.436 0 2.437.002 3.192.103c.734.099 1.122.28 1.4.556c.276.277.456.665.555 1.4c.102.754.103 1.756.103 3.191v8c0 1.435-.001 2.436-.103 3.192c-.099.734-.279 1.122-.556 1.399s-.665.457-1.399.556c-.755.101-1.756.103-3.192.103h-1c-1.435 0-2.436-.002-3.192-.103c-.733-.099-1.122-.28-1.399-.556c-.243-.244-.41-.572-.515-1.138c-.108-.589-.136-1.364-.142-2.457a.75.75 0 1 0-1.5.008c.006 1.082.032 1.983.167 2.72c.14.758.405 1.403.93 1.928c.601.602 1.36.86 2.26.982c.866.116 1.969.116 3.336.116h1.11c1.368 0 2.47 0 3.337-.116c.9-.122 1.658-.38 2.26-.982s.86-1.36.982-2.26c.116-.867.116-1.97.116-3.337v-8.11c0-1.367 0-2.47-.116-3.337c-.121-.9-.38-1.658-.982-2.26s-1.36-.86-2.26-.981c-.867-.117-1.97-.117-3.337-.117z" />
                    <path fill="currentColor" d="M15 11.25a.75.75 0 0 1 0 1.5H4.027l1.961 1.68a.75.75 0 1 1-.976 1.14l-3.5-3a.75.75 0 0 1 0-1.14l3.5-3a.75.75 0 1 1 .976 1.14l-1.96 1.68z" />
                </svg>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
<div id="overlay" class="overlay"></div>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        width: 250px;
        background-color: #333;
        color: #fff;
        position: fixed;
        top: 0;
        left: -250px;
        height: 100%;
        transition: left 0.3s;
        display: flex;
        flex-direction: column;
        z-index: 1000000;
        overflow-y: scroll;
    }

    .sidebar-header {
        padding: 10px;
        background: #444;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-menu {
        list-style-type: none;
        padding: 0;
        margin: 0;
        flex: 1;
    }

    .sidebar-menu li a {
        color: #fff;
        text-decoration: none;
        padding: 20px;
        display: flex;
        align-items: center;
    }

    .sidebar-menu li a span {
        margin-left: 5px;
    }

    .sidebar-menu li a:hover {
        background: #555;
        color: var(--primary);
    }

    .close-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 30px;
        cursor: pointer;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
    }

    .overlay.show {
        display: block;
    }

    .menu-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }

    main {
        padding: 20px;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
            left: -200px;
        }

        .content {
            margin-left: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menuBtn');
        const closeBtn = document.getElementById('closeBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function openSidebar() {
            sidebar.style.left = '0';
            overlay.classList.add('show');
        }

        function closeSidebar() {
            sidebar.style.left = '-250px';
            overlay.classList.remove('show');
        }

        menuBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

    });
</script>