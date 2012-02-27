PHAR = webpage_consolidator.phar

SOURCE = \
	simplehtmldom/simple_html_dom.php \
	WebpageConsolidator.inc.php \
	WebpageCache.inc.php \
	thrive/Autoloader.php \
	thrive/Thrive.php \
	thrive/flourish/classes/fCache.php \
	index.php

$(PHAR): $(SOURCE) clean package
	@echo "2. Generating certificates..."
	@mkdir cert
	@cd cert; phar-generate-cert

	echo "3. Creating $(PHAR) phar..."
	@phar-build --phar $(PHAR) --stub=stub.php
	@chmod -vf 0755 webpage_consolidator.phar

package:
	@echo "1. Packaging source files..."
	@mkdir -p src
	@rm -rf src/*
	@rsync -av --relative $(SOURCE) src

clean:
	@echo "Cleaning up..."
	@rm -rf src cert webpage_consolidator.phar*

dist:
	rm -rf src cert

