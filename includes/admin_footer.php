        </main>
    </div>
</div>

<div class="scroll-nav">
    <button class="scroll-btn" id="scrollTopBtn" title="Scroll to top" aria-label="Scroll to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>
    <button class="scroll-btn" id="scrollBottomBtn" title="Scroll to bottom" aria-label="Scroll to bottom" onclick="window.scrollTo({top:document.documentElement.scrollHeight,behavior:'smooth'})">
        <i class="fas fa-arrow-down"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var sidebar = document.getElementById('adminSidebar');
    var wrapper = document.querySelector('.admin-wrapper');
    var toggleBtn = document.getElementById('sidebarToggle');
    var isMobile = function() { return window.innerWidth < 992; };

    function applyCollapsedState() {
        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
            wrapper.classList.add('sidebar-collapsed');
        } else {
            wrapper.classList.remove('sidebar-collapsed');
        }
    }

    applyCollapsedState();
    window.addEventListener('resize', applyCollapsedState);

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isMobile()) {
                sidebar.classList.toggle('show');
            } else {
                wrapper.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (isMobile() && sidebar.classList.contains('show') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });

    var scrollTopBtn = document.getElementById('scrollTopBtn');
    var scrollBottomBtn = document.getElementById('scrollBottomBtn');

    function updateScrollBtns() {
        var scrollY = window.scrollY || window.pageYOffset;
        var docHeight = document.documentElement.scrollHeight;
        var winHeight = window.innerHeight;
        var atTop = scrollY < 200;
        var atBottom = scrollY + winHeight >= docHeight - 100;
        var hasScroll = docHeight > winHeight + 200;

        if (hasScroll && !atTop) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }

        if (hasScroll && !atBottom) {
            scrollBottomBtn.classList.add('visible');
        } else {
            scrollBottomBtn.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', updateScrollBtns);
    window.addEventListener('resize', updateScrollBtns);
    updateScrollBtns();

    var sectionLabels = document.querySelectorAll('.sidebar-section-label[data-section]');
    var savedSections = {};
    try { savedSections = JSON.parse(localStorage.getItem('sidebarSections') || '{}'); } catch(e) {}

    sectionLabels.forEach(function(label) {
        var sectionKey = label.getAttribute('data-section');
        var list = document.querySelector('[data-section-list="' + sectionKey + '"]');
        if (!list) return;

        if (savedSections[sectionKey] === false) {
            label.classList.add('collapsed');
            list.classList.add('section-hidden');
            label.setAttribute('aria-expanded', 'false');
        }

        function toggleSection() {
            label.classList.toggle('collapsed');
            list.classList.toggle('section-hidden');
            var isExpanded = !label.classList.contains('collapsed');
            label.setAttribute('aria-expanded', isExpanded);
            savedSections[sectionKey] = isExpanded;
            localStorage.setItem('sidebarSections', JSON.stringify(savedSections));
        }

        label.addEventListener('click', toggleSection);
        label.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSection();
            }
        });
    });
})();

function toggleFullscreen() {
    var icon = document.getElementById('fullscreenIcon');
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(function() {
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        }).catch(function() {});
    } else {
        document.exitFullscreen().then(function() {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }).catch(function() {});
    }
}

document.addEventListener('fullscreenchange', function() {
    var icon = document.getElementById('fullscreenIcon');
    if (icon) {
        if (document.fullscreenElement) {
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }
});
</script>
</body>
</html>
