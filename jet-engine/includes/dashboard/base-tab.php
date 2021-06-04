<?php

namespace Jet_Engine\Dashboard;

abstract class Base_Tab {

	abstract public function slug(): string;

	abstract public function label(): string;

	abstract public function load_config(): array;

	public function condition(): bool {
	    return true;
    }

	public function render_tab() {
		?>
        <cx-vui-tabs-panel
                name="<?= $this->slug() ?>"
                label="<?= $this->label() ?>"
                key="<?= $this->slug() ?>"
        >
            <keep-alive>
                <jet-engine-tab-<?= $this->slug() ?> />
            </keep-alive>
        </cx-vui-tabs-panel>
		<?php
	}

	public function render_assets() {
	}


}