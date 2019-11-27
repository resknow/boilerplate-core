const pluginList = new Reef('#plugin-list', {
    data: {
        alert: false,
        plugins: false,
        installedPlugins: []
    },
    template: (props) => {
        if ( props.plugins ) {

            // Create Plugin Template
            let plugins = props.plugins.map((plugin) => {

                let status = 'install';

                // Is this plugin installed?
                if ( props.installedPlugins.includes(plugin.slug) ) {
                    status = 'delete';
                }

                return `<article class="trace-item trace-item--setup">
                    <div class="plugin">
                        <div class="plugin__info">
                            <h4>${plugin.name}</h4>
                            <p>${plugin.description}</p>
                        </div>
                        <div class="plugin__actions">
                            <button class="btn btn--${status}" data-status="${status}" data-name="${plugin.slug}" data-plugin="${plugin.download_url}">${status}</button>
                        </div>
                    </div>
                </article>`;
            });

            // Get alert
            let alert = '';
            if ( props.alert ) {
                alert = `<div class="alert">${props.alert}</div>`;
            }

            return `${alert} ${plugins.join('')}`;
        }

        // No plugins loaded yet
        return `<div class="trace-item trace-item--setup">
            <div class="loader"></div>
        </div>`;

    }
});

const loadPlugins = (app) => {
    fetch('https://plugins.resknow.net').then((res) => {
        return res.json();
    }).then((res) => {
        app.setData({
            plugins: res
        });
    });
}

const installedPlugins = (app) => {
    fetch(`${window.pkgDir}/core/ui/get-installed-plugins.php`).then((res) => {
        return res.json();
    }).then((res) => {
        app.setData({
            installedPlugins: res.plugins
        });
    });
}

const installPlugin = (app, url, name) => {
    fetch(`${window.pkgDir}/core/ui/install-plugin.php?url=${url}`).then((res) => {
        return res.json();
    }).then((res) => {

        if ( res.code == 200 ) {

            // Get app data
            let data = app.getData();

            // Get installed list
            let installed = data.installedPlugins;

            // Update it
            installed.push(name);

            // Set it
            app.setData({
                installedPlugins: installed
            });
        } else {
            app.setData({alert: res.message});
        }

    });
}

const deletePlugin = (app, name) => {
    fetch(`${window.pkgDir}/core/ui/delete-plugin.php?name=${name}`).then((res) => {
        return res.json();
    }).then((res) => {
        // Update the installed plugins list
        installedPlugins(app);
    });
}

// Handle Installation
document.addEventListener('click', (event) => {

    // Get Plugin URL
    let url = event.target.getAttribute('data-plugin');
    if ( !url ) return;

    // Get Plugin Name
    let name = event.target.getAttribute('data-name');
    if ( !name ) return;

    // Deletion
    let status = event.target.getAttribute('data-status');
    if ( status === 'delete' ) {
        deletePlugin(pluginList, name);
        return;
    }

    // Install the plugin
    installPlugin(pluginList, url, name);

});

document.addEventListener('DOMContentLoaded', () => {
    pluginList.render();

    // Load Plugins
    loadPlugins(pluginList);

    // Get installed plugins
    installedPlugins(pluginList);
});
