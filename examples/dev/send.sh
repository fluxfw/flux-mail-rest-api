#!/usr/bin/env sh

set -e

curl -X POST -H "Content-Type:application/json" -d '{"subject":"Test Mail Subject '$(date +%s)'","body_html":"<h1>Test Mail Body HTML</h1><p>dahsdgahsdgahdahd</p>","body_text":"Test Mail Text\n\ndahsdgahsdgahdahd","to":[{"email":"to@test.local","name":"Test To User"}],"attachments":[{"name":"example.txt","data":"Test\ndasgdhagsdhafdasd"}]}' http://%host%:9501/send
