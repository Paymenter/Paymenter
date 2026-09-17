<?php

namespace Paymenter\Extensions\Servers\Pterodactyl;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Server;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class Pterodactyl
 */
#[ExtensionMeta(
    name: 'Pterodactyl',
    description: 'Pterodactyl server extension',
    version: 'builtin',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/pterodactyl',
    icon: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iMTA0IiB2aWV3Ym94PSIwIDAgNjQgMTA0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJob3ZlcjpzY2FsZS0xMjAgZmxleCBpdGVtcy1jZW50ZXIganVzdGlmeS1jZW50ZXIgc2l6ZS0xOCByb3VuZGVkLXhsIHAtNCBiZy1bIzEwNTI5Rl0gcmluZy02IHJpbmctWyMxMDUyOUZdLzQwIj48cGF0aCBkPSJNNTUuMzIwNSA1MC45NDlDNTUuMzIwNSA1MC45NDkgNTUuMTQ1MiA1MS43NDQyIDU1LjE0NTIgNTEuODc2NkM1NS4xNDUyIDUyLjAwOSA1MC41NTEzIDUxLjQ3OTQgNTAuNTUxMyA1MS40Nzk0TDUwLjcyNjYgNTAuMDIxNUw1NS4zMjA1IDUwLjk0OVoiIGZpbGw9IiM5MDRBMDQiPjwvcGF0aD48cGF0aCBkPSJNNTQuNTMwNyA1MC41OTM4QzU0LjUzMDcgNTAuNTkzOCA0Mi44NjQ0IDQ3LjEwNjMgMzcuODI5NSA0OC4wMzM5QzMyLjc5NDYgNDguOTYxNCAzNi4wNTY5IDUxLjY3ODMgMzYuMDU2OSA1MS42NzgzTDU0LjUzMDcgNTAuNTkzOFoiIGZpbGw9IiM5MDRBMDQiPjwvcGF0aD48cGF0aCBkPSJNOC4yNzU4OCA1MS4wODE4QzguMjc1ODggNTEuMDgxOCA4LjQ1MTIyIDUxLjg3NyA4LjQ1MTIyIDUyLjAwOTRDOC40NTEyMiA1Mi4xNDE4IDEzLjA0NDMgNTEuNjEyMiAxMy4wNDQzIDUxLjYxMjJMMTIuODY4OSA1MC4xNTQzTDguMjc1ODggNTEuMDgxOFoiIGZpbGw9IiM5MDRBMDQiPjwvcGF0aD48cGF0aCBkPSJNOS4wNjU5MiA1MC43Mjg4QzkuMDY1OTIgNTAuNzI4OCAyMC43MzIyIDQ3LjI0MDQgMjUuNzY3IDQ4LjE2ODlDMzAuODAxOSA0OS4wOTczIDI3LjUzOTcgNTEuODEzMyAyNy41Mzk3IDUxLjgxMzNMOS4wNjU5MiA1MC43Mjg4WiIgZmlsbD0iIzkwNEEwNCI+PC9wYXRoPjxwYXRoIGQ9Ik0zMi41MjYgNzAuMDMwNUM0NC4zMDgyIDcwLjAzMDUgNTMuODU5NiA2NS4xNjU3IDUzLjg1OTYgNTkuMTY0NkM1My44NTk2IDUzLjE2MzYgNDQuMzA4MiA0OC4yOTg4IDMyLjUyNiA0OC4yOTg4QzIwLjc0MzggNDguMjk4OCAxMS4xOTI0IDUzLjE2MzYgMTEuMTkyNCA1OS4xNjQ2QzExLjE5MjQgNjUuMTY1NyAyMC43NDM4IDcwLjAzMDUgMzIuNTI2IDcwLjAzMDVaIiBmaWxsPSIjQjI2NzM0Ij48L3BhdGg+PHBhdGggZD0iTTI2LjE2NDkgNDguODMwMUwxNC4zNzE1IDUwLjY4NTJMNi43NjcwMSA1MS44NTAzTDQuNzE3MjkgNTMuOTU0NFY1Ny42ODEyTDUuMTA1NjYgNjIuMzE5TDExLjUzNDUgNjQuNjM3OUwxNi41Mzk2IDY0Ljk2OTJDMTUuMDcyOSA1MC4wNTQ4IDI2LjE2NDkgNDguODMwMSAyNi4xNjQ5IDQ4LjgzMDFaIiBmaWxsPSIjQjI2NzM0Ij48L3BhdGg+PHBhdGggZD0iTTIwLjMwNDEgNjAuNjIyNkMyMC4zMDQxIDYwLjYyMjYgMTcuOTE5NSA1Ni45MTI0IDExLjY5MTQgNTguODk5OUM1LjQ2MzMzIDYwLjg4NzQgMTUuMTM2OCA2Mi42MTAxIDE1LjEzNjggNjIuNjEwMUwyMC4zMDQxIDYwLjYyMjZaIiBmaWxsPSIjNkQzQzBGIj48L3BhdGg+PHBhdGggZD0iTTIuNDQ1OCA5MC43OTIyQzIuNDQ1OCA5MC43OTIyIDYuNTY2MjkgODYuMjAzNSAxNS45MjQyIDg1LjQ2MzZDMTkuNTkwNSA4NS4xNzQzIDE4LjcyOTYgOTMuNzczIDE4LjcyOTYgOTMuNzczTDI0Ljk3NTIgODguMTk2M0wzNy4xODg1IDg5LjE5MDRMNDIuMTM3NSA5Mi44MzQ5QzQyLjEzNzUgOTIuODM0OSA0NS4zMDE1IDg1LjAwMDcgNTEuMzM1IDg1LjE2MjlDNTcuMzY4NCA4NS4zMjUxIDYxLjAxMTEgOTAuMTExIDYxLjAxMTEgOTAuMTExTDUzLjM0NjEgNjUuOTE0MUg4Ljk3ODk3TDIuNDQ1OCA5MC43OTIyWiIgZmlsbD0iIzZEM0MwRiI+PC9wYXRoPjxwYXRoIGQ9Ik00Mi45NDA1IDcxLjcxMDdDNDAuNDYzOSA4MS45MTQ2IDM5LjgzOTYgOTIuNDAwOCAzMS45OTMyIDkyLjQwMDhDMjQuMTYzNCA5Mi40MDA4IDIzLjYxMTkgODMuMDExNCAyMS40NTcgNzIuNTE3M0MxOS40ODc5IDYyLjkyNjIgMjUuNjk4NSA1Ni41MjU0IDMxLjk5MzIgNTYuNTI1NEMzNC4wMTYgNTYuNTQxMiAzNS45OTg5IDU3LjA4OTQgMzcuNzQyNiA1OC4xMTQ4QzQxLjcwOTYgNjAuNDI4NSA0NC41Mjc0IDY1LjE3NjcgNDIuOTQwNSA3MS43MTA3WiIgZmlsbD0iI0IyNjczNCI+PC9wYXRoPjxwYXRoIGQ9Ik0zOS44MTMxIDc0LjQ2MTlDMzkuODEzMSA4MS44Mzk0IDM0LjY2NzcgOTAuMDQzNSAzMC44MDI0IDg4LjM2MkMyNi43NDMyIDg2LjU5NDYgMjQuNTk0NSA4MS4zNDg0IDIzLjc4ODggNzMuNzAzNkMyMy4wMTk5IDY2LjQwODYgMjcuNjY1NSA2MS4wMDIgMzEuNzAzNiA2MS4wMDJDMzUuNzQxNyA2MS4wMDIgMzkuODEzMSA2Ny4wODM2IDM5LjgxMzEgNzQuNDYxOVoiIGZpbGw9IiNCRDZFMzUiPjwvcGF0aD48cGF0aCBkPSJNMzcuNDMxMiA0OC42OTM0TDQ5LjIyNDUgNTAuNTUyTDU2LjgyNjQgNTEuNzEyN0w1OC44NzYxIDUzLjgxNjhWNTcuNTQzNkw1OC40OTIxIDYyLjE3ODhMNTIuMDYzMyA2NC40OTc2TDQ3LjA1ODIgNjQuODI5QzQ4LjUyMzIgNDkuOTIwNyAzNy40MzEyIDQ4LjY5MzQgMzcuNDMxMiA0OC42OTM0WiIgZmlsbD0iI0IyNjczNCI+PC9wYXRoPjxwYXRoIGQ9Ik0yMy45MTE2IDQ5LjIyNzlMMjguNTE0MyA1OS4xNjYyTDMzLjE1NTUgNjQuMDM3MUwzNi45MjU0IDU4Ljc3NjlMNDEuMjcwMyA0OS4yMjc5TDM0Ljk1OCA0OC4xNjhMMjguODEyNCA0OC42MjNMMjMuOTExNiA0OS4yMjc5WiIgZmlsbD0iI0E1NjAzMiI+PC9wYXRoPjxwYXRoIGQ9Ik00Mi4yODg4IDY0LjU5MjVDNDIuMjg4OCA2NC41OTI1IDQzLjQ5OTUgNzAuODI2NyA0MS4xMTQgNzEuNjIxOUMzOC43Mjg1IDcyLjQxNyAzNi40NzYzIDY2LjQ1MzcgMzYuNDc2MyA2Ni40NTM3SDMxLjQ0MTRMMzEuMzA4MSA2My4yNzM5TDM5LjUyMzcgNjEuNjgzNkw0Mi4yODg4IDY0LjU5MjVaIiBmaWxsPSIjM0EzQTNBIj48L3BhdGg+PHBhdGggZD0iTTIxLjk0MTcgNjQuNTkyNUMyMS45NDE3IDY0LjU5MjUgMjAuNzMxOCA3MC44MjY3IDIzLjExNjQgNzEuNjIxOUMyNS41MDExIDcyLjQxNyAyNy43NTQyIDY2LjQ1MzcgMjcuNzU0MiA2Ni40NTM3SDMyLjc4OTlMMzIuOTIyMyA2My4yNzM5TDI0LjcwNjggNjEuNjgzNkwyMS45NDE3IDY0LjU5MjVaIiBmaWxsPSIjM0EzQTNBIj48L3BhdGg+PHBhdGggZD0iTTI0LjMwOTYgNjUuNzkxNkwyNS41MDE5IDY0Ljk5NzNMMjYuMDMyMyA2NC4zMzQ1SDM4LjIyMjhMMzguNjE5OSA2NC45OTczTDM5LjY3OTkgNjUuNTI2OEwzOC44ODQ3IDYxLjgxNjZMMzAuODAyNCA2MS4yODcxTDI1LjM2OTUgNjEuODE2NkwyNC4zMDk2IDY1Ljc5MTZaIiBmaWxsPSIjNDQ0NDQ0Ij48L3BhdGg+PHBhdGggZD0iTTQyLjIyOCA2Mi41NTUyQzQyLjIyOCA2Mi41NTUyIDQyLjg4OTkgNjUuNDAzNiA0MC45MDI0IDY1Ljk5NzJDMzguOTE0OSA2Ni41OTA3IDM4LjI0OTUgNjIuNDgxNiAzOC4yNDk1IDYyLjQ4MTZMMzkuNzEwMSA2Mi4wODAxTDQyLjIyOCA2Mi41NTUyWiIgZmlsbD0iIzUxNTE1MSI+PC9wYXRoPjxwYXRoIGQ9Ik0yMS45MjQ5IDYyLjU1NTJDMjEuOTI0OSA2Mi41NTUyIDIxLjI2MjIgNjUuNDAzNiAyMy4yNDk2IDY1Ljk5NzJDMjUuMjM3MSA2Ni41OTA3IDI1LjkwMjUgNjIuNDgxNiAyNS45MDI1IDYyLjQ4MTZMMjQuNDQyIDYyLjA4MDFMMjEuOTI0OSA2Mi41NTUyWiIgZmlsbD0iIzUxNTE1MSI+PC9wYXRoPjxwYXRoIGQ9Ik0zNC42NjI2IDYwLjA5MzhINDAuMDc4TDQyLjMzMDIgNjIuNjExNkwzOS45NDU2IDYyLjc0NEwzNy4wMjk3IDYyLjM0NjlMMzQuNzc3NCA2Mi42MTE2TDM0LjY2MjYgNjAuMDkzOFoiIGZpbGw9IiMzMDMwMzAiPjwvcGF0aD48cGF0aCBkPSJNMjkuNjA5NSA2MC4wOTM4SDI0LjE5NDFMMjEuOTQxOSA2Mi42MTE2TDI0LjMyNzQgNjIuNzQ0TDI3LjI0MjQgNjIuMzQ2OUwyOS40OTQ3IDYyLjYxMTZMMjkuNjA5NSA2MC4wOTM4WiIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik0zNC43Nzc2IDYyLjYxMTZIMjkuMzQ0N0wyOC4xNTIzIDYwLjA5MzhIMzYuMjM1NUwzNC43Nzc2IDYyLjYxMTZaIiBmaWxsPSIjNTE1MTUxIj48L3BhdGg+PHBhdGggZD0iTTM0LjY0NTcgNjEuNDE3M0MzNC44NjUgNjEuNDE3MyAzNS4wNDI4IDYxLjIzOTUgMzUuMDQyOCA2MS4wMjAyQzM1LjA0MjggNjAuODAwOSAzNC44NjUgNjAuNjIzIDM0LjY0NTcgNjAuNjIzQzM0LjQyNjMgNjAuNjIzIDM0LjI0ODUgNjAuODAwOSAzNC4yNDg1IDYxLjAyMDJDMzQuMjQ4NSA2MS4yMzk1IDM0LjQyNjMgNjEuNDE3MyAzNC42NDU3IDYxLjQxNzNaIiBmaWxsPSIjQUFBQUFBIj48L3BhdGg+PHBhdGggZD0iTTMyLjcwMTQgNjEuODE2NEgzMS41NTJDMzEuNTAzNiA2MS44MTY0IDMxLjQ2NDQgNjEuODU1NyAzMS40NjQ0IDYxLjkwNDFWNjEuOTkzNUMzMS40NjQ0IDYyLjA0MTkgMzEuNTAzNiA2Mi4wODEyIDMxLjU1MiA2Mi4wODEySDMyLjcwMTRDMzIuNzQ5OCA2Mi4wODEyIDMyLjc4OSA2Mi4wNDE5IDMyLjc4OSA2MS45OTM1VjYxLjkwNDFDMzIuNzg5IDYxLjg1NTcgMzIuNzQ5OCA2MS44MTY0IDMyLjcwMTQgNjEuODE2NFoiIGZpbGw9IiNBQUFBQUEiPjwvcGF0aD48cGF0aCBkPSJNMjMuNTYxNCA2NS40NDY3QzIzLjk3MTggNjQuNjkwOSAyNC4yMzkzIDYzLjg2NTkgMjQuMzUwNCA2My4wMTNDMjQuMzYwOSA2Mi45MjUzIDI0LjIyODUgNjIuOTI1MyAyNC4yMTggNjMuMDEzQzI0LjExMDMgNjMuODQyNiAyMy44NTAzIDY0LjY0NTEgMjMuNDUwOSA2NS4zODAxQzIzLjQxMDYgNjUuNDU0NiAyMy41MjQ1IDY1LjUyMjEgMjMuNTY1NyA2NS40NDY3SDIzLjU2MTRaIiBmaWxsPSIjM0EzQTNBIj48L3BhdGg+PHBhdGggZD0iTTQwLjYzNDIgNjUuMzgwMUM0MC4yMzUxIDY0LjY0NSAzOS45NzUzIDYzLjg0MjUgMzkuODY3OSA2My4wMTNDMzkuODU2NSA2Mi45MjUzIDM5LjcyNDIgNjIuOTI1MyAzOS43MzU2IDYzLjAxM0MzOS44NDU4IDYzLjg2NjEgNDAuMTEzMyA2NC42OTEzIDQwLjUyNDYgNjUuNDQ2N0M0MC41NjQ5IDY1LjUyMjEgNDAuNjc4OSA2NS40NTU1IDQwLjYzODYgNjUuMzgwMUg0MC42MzQyWiIgZmlsbD0iIzNBM0EzQSI+PC9wYXRoPjxwYXRoIGQ9Ik0yNy4zNTM3IDYxLjI5ODNDMTUuNzU5NCA3Mi41NjEzIDEzLjI4ODkgOTQuNjUyMyAxMy4yODg5IDk0LjY1MjNDMTMuMjg4OSA5NC42NTIzIDExLjA1MjQgODQuNjUyNyA4LjgxNzY4IDg0LjI4MDFDNS44MzY5IDgzLjc4MyAyLjUwOTgzIDkwLjM5NDIgMi41MDk4MyA5MC4zOTQyQzIuNTA5ODMgOTAuMzk0MiA1LjUxODY2IDc4LjA4MTggNC40Mzg1NyA3My43NDgzQzMuNDI0MjIgNjkuNjYzOCAtMi4xMDE2MiA2My40NDQ1IDEuMjEwNTYgNTUuODI1OUMxLjIxMDU2IDU1LjgyNTkgNC4xNTQ1MSA1MS4wMDQxIDExLjU5NDIgNTAuMzAyN0MxMS41OTQyIDUwLjMwMjcgLTExLjI5NjQgNTcuMjY5IDI2Ljc0NDQgNjAuOTY4N0gyOC4wMTM5TDI3LjM1MzcgNjEuMjk4M1oiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNMjQuMjYzMyA2Mi43MTA0QzE1LjAzODcgNzEuNjcxMiAxMy4yMDQ2IDkwLjA5MDYgMTMuMjA0NiA5MC4wOTA2QzEzLjIwNDYgOTAuMDkwNiAxMS4xMDU4IDgyLjEzNDYgOS4zMjY5OCA4MS44MzgyQzYuOTU1NTEgODEuNDQyOSA0LjE0ODMxIDg2LjYzOTEgNC4xNDgzMSA4Ni42MzkxQzQuMTQ4MzEgODYuNjM5MSA3LjMyMTk3IDc2LjY5MDMgNS44NDU2IDczLjQ1ODdDMS41NDAxMyA2NC4wMzY5IDEuMjc1MzcgNjEuNDgzOSAxLjY3NjAyIDU5LjAwNDZDMS42NzYwMiA1OS4wMDQ2IDEuNjgzMDMgNTQuNDg1MiA2LjAzMjM0IDUyLjUyOTNDNi4wMzIzNCA1Mi41MjkzIC02LjQ3ODE2IDU5LjI2OTQgMjMuNzg4MSA2Mi4yMTMzSDI0Ljc4NjdMMjQuMjYzMyA2Mi43MTA0WiIgZmlsbD0iIzkwNEEwNCI+PC9wYXRoPjxwYXRoIGQ9Ik00NC4wODY0IDYwLjQ4OThDNDQuMDg2NCA2MC40ODk4IDQ2LjQ3MTkgNTYuNzc5NiA1Mi43IDU4Ljc2NzFDNTguOTI4MSA2MC43NTQ2IDQ5LjI1NDYgNjIuNDc3MyA0OS4yNTQ2IDYyLjQ3NzNMNDQuMDg2NCA2MC40ODk4WiIgZmlsbD0iIzZEM0MwRiI+PC9wYXRoPjxwYXRoIGQ9Ik0zNi4yNDI5IDYxLjE2MzVDNDcuODM3MiA3Mi40Mjc0IDUwLjMwNzggOTQuNTE4NSA1MC4zMDc4IDk0LjUxODVDNTAuMzA3OCA5NC41MTg1IDUyLjU0NDIgODQuNTE3OSA1NC43Nzg5IDg0LjE0NTNDNTcuNzU5NyA4My42NDkxIDYxLjA5MTIgOTAuMjYwMyA2MS4wOTEyIDkwLjI2MDNDNjEuMDkxMiA5MC4yNjAzIDU4LjA4MjMgNzcuOTQ4IDU5LjE2MjQgNzMuNjE0NEM2MC4xNzY4IDY5LjUyOTkgNjUuNzAyNiA2My4zMTA2IDYyLjM5MDQgNTUuNjkxMkM2Mi4zOTA0IDU1LjY5MTIgNTkuNDQ2NSA1MC44NjkzIDUyLjAwNjggNTAuMTY4QzUyLjAwNjggNTAuMTY4IDc0Ljg5NzQgNTcuMTM0MiAzNi44NTY2IDYwLjgzMzlIMzUuNTgwMUwzNi4yNDI5IDYxLjE2MzVaIiBmaWxsPSIjQTQ1NzFGIj48L3BhdGg+PHBhdGggZD0iTTM5LjMzMyA2Mi41NzhDNDguNTU3NiA3MS41Mzg3IDUwLjM5MTcgODkuOTU4MiA1MC4zOTE3IDg5Ljk1ODJDNTAuMzkxNyA4OS45NTgyIDUyLjQ4OTYgODIuMDAyMiA1NC4yNjg0IDgxLjcwNUM1Ni42NDA4IDgxLjMwOTYgNTkuNDQ4IDg2LjUwNjYgNTkuNDQ4IDg2LjUwNjZDNTkuNDQ4IDg2LjUwNjYgNTYuMjczNCA3Ni41NTc5IDU3Ljc0OTggNzMuMzI5OEM2Mi4wNTYxIDYzLjkwMzYgNjIuMzIwOSA2MS4zNTUgNjEuOTIwMyA1OC44NzU3QzYxLjkyMDMgNTguODc1NyA2MS45MTMyIDU0LjM1NjMgNTcuNTYzOSA1Mi40MDA0QzU3LjU2MzkgNTIuNDAwNCA3MC4wNzQ0IDU5LjE0MDUgMzkuODA3MyA2Mi4wODM1SDM4LjgwOTZMMzkuMzMzIDYyLjU3OFoiIGZpbGw9IiM5MDRBMDQiPjwvcGF0aD48cGF0aCBkPSJNMTcuMTk5NyA1MC4xODQzQzE3LjE5OTcgNTAuMTg0MyAxOS4xMjA2IDUzLjg2NjUgMjYuNTgwNCA1My44NjY1QzI2LjkzMTEgNTMuODY2NSAyNS44MDEgNTMuMzA1NCAyNS44MDEgNTMuMzA1NEMyNS44MDEgNTMuMzA1NCAyMC45ODcxIDUzLjU4MDcgMTcuOTY0MiA1MC4xMTk0QzE3LjYzODEgNDkuNzQ2IDE3LjE5OTcgNTAuMTg0MyAxNy4xOTk3IDUwLjE4NDNaIiBmaWxsPSIjQTU2MDMyIj48L3BhdGg+PHBhdGggZD0iTTguMDU0MiA1OC45ODgzVjYyLjM5MzRMOS43MDc2NiA1OS40ODhMOC4wNTQyIDU4Ljk4ODNaIiBmaWxsPSIjQTQ1NzFGIj48L3BhdGg+PHBhdGggZD0iTTcuMDIwNTEgNTguMjY1NlY2MC43Mjc0TDguMjE2MzMgNTguNjI2TDcuMDIwNTEgNTguMjY1NloiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNNTUuNTQyMSA1OC44NTc0VjYyLjI2MjVMNTMuODg4NyA1OS4zNTcxTDU1LjU0MjEgNTguODU3NFoiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNNTYuNTc0OCA1OC4xMzA5VjYwLjU5MzVMNTUuMzc5OSA1OC40OTIxTDU2LjU3NDggNTguMTMwOVoiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNMjQuMjEwNyA0OC4wMTc2TDIzLjg4MjggNDkuMTg4OEwyNi42OTQ0IDQ5LjIyNzRMMjYuODI2OCA0OC4xNjc1TDI0LjIxMDcgNDguMDE3NloiIGZpbGw9IiM4MjQwMDkiPjwvcGF0aD48cGF0aCBkPSJNNDEuMzUzNiA0Ny45MzQ0TDQxLjI1NzEgNDkuMjQ4NUwzOC44NTUgNDguNzgzOUwzOS4wMTggNDcuNzY5NUw0MS4zNTM2IDQ3LjkzNDRaIiBmaWxsPSIjODI0MDA5Ij48L3BhdGg+PHBhdGggZD0iTTI4LjI1NDMgOTMuMTQxOEMyNy4xNDI2IDk2LjQwNCAyNi4zNTg4IDk4LjcwMDEgMjQuNTkwNSA5OC43MDAxQzIyLjgyMjIgOTguNzAwMSAyMC45ODczIDk1LjQ3NzMgMjAuOTg3MyA5Mi4wMzFDMjAuOTg3MyA4OC41ODQ3IDIyLjU5NTIgODQuMTUwNCAyNC4zNjQzIDg0LjE1MDRDMjYuMTMzNSA4NC4xNTA0IDI5LjQ5NjYgODkuNDk1NiAyOC4yNTQzIDkzLjE0MThaIiBmaWxsPSIjQjI2NzM0Ij48L3BhdGg+PHBhdGggZD0iTTI2LjQ2NTYgMTAwLjA3OEwyMi43ODYxIDk5Ljc0M0wyMy43NDM1IDk2Ljk2NzNMMjYuMTYwNiA5Ni44NjkxTDI2LjQ2NTYgMTAwLjA3OFoiIGZpbGw9IiNCMjY3MzQiPjwvcGF0aD48cGF0aCBkPSJNMjIuODkxNCA5OS40Mzc1TDI2LjQ0OSA5OS45MDkyTDI4LjA2MyAxMDMuOTI2QzI0LjEwODIgOTkuNTk4OCAyMC4xNTQzIDEwMy4zMDUgMjAuMTU0MyAxMDMuMzA1TDIyLjg5MTQgOTkuNDM3NVoiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNMjMuMDk0NiAxMDEuMjgxQzIzLjA5NDYgMTAxLjI4MSAyMS44OTQ0IDEwMy4wNTEgMjIuMzA1NSAxMDMuOTI2TDIzLjYzMDIgMTAyLjYwNEwyNS4xOTI1IDEwMS4yODFIMjMuMDk0NloiIGZpbGw9IiNBNDU3MUYiPjwvcGF0aD48cGF0aCBkPSJNMzUuNzcyNSA5Mi41MTg5QzM2Ljc1MjcgOTUuODIyMyAzNy4xNjA0IDk4LjIyNTMgMzguOTI4NyA5OC4yMjUzQzQwLjY5NyA5OC4yMjUzIDQyLjUzMTkgOTUuMDAyNSA0Mi41MzE5IDkxLjU1NjJDNDIuNTMxOSA4OC4xMDk5IDQwLjkyMzIgODMuNjczOCAzOS4xNTQ5IDgzLjY3MzhDMzcuMzg2NiA4My42NzM4IDM0LjY5NiA4OC44ODkzIDM1Ljc3MjUgOTIuNTE4OVoiIGZpbGw9IiNCMjY3MzQiPjwvcGF0aD48cGF0aCBkPSJNMzcuMDUzNyA5OS42MDUyTDQwLjczMjMgOTkuMjcwM0wzOS43NzUgOTYuNDk0N0wzNy4zNTg4IDk2LjM5NjVMMzcuMDUzNyA5OS42MDUyWiIgZmlsbD0iI0IyNjczNCI+PC9wYXRoPjxwYXRoIGQ9Ik00MC42MjczIDk4Ljk2NDhMMzcuMDY5NyA5OS40MzY1TDM1LjQ1NjUgMTAzLjQ1NEMzOS40MTA1IDk5LjEyNjIgNDMuMzY1MyAxMDIuODMyIDQzLjM2NTMgMTAyLjgzMkw0MC42MjczIDk4Ljk2NDhaIiBmaWxsPSIjQTQ1NzFGIj48L3BhdGg+PHBhdGggZD0iTTQwLjEyNTcgMTAwLjg3M0M0MC4xMjU3IDEwMC44NzMgNDEuMzI2OCAxMDIuNjQzIDQwLjkxNDcgMTAzLjUxOEwzOS41ODkxIDEwMi4xOTVMMzguMDI2OSAxMDAuODczSDQwLjEyNTdaIiBmaWxsPSIjQTQ1NzFGIj48L3BhdGg+PHBhdGggZD0iTTI3Ljc0NDUgNTMuNTU0N0gyNC45ODM4QzI0LjY4NTEgNTMuNTU0NyAyNC40NDI5IDUzLjc5NjkgMjQuNDQyOSA1NC4wOTU2VjU0LjczNjVDMjQuNDQyOSA1NS4wMzUyIDI0LjY4NTEgNTUuMjc3NCAyNC45ODM4IDU1LjI3NzRIMjcuNzQ0NUMyOC4wNDMzIDU1LjI3NzQgMjguMjg1NCA1NS4wMzUyIDI4LjI4NTQgNTQuNzM2NVY1NC4wOTU2QzI4LjI4NTQgNTMuNzk2OSAyOC4wNDMzIDUzLjU1NDcgMjcuNzQ0NSA1My41NTQ3WiIgZmlsbD0iIzNGM0YzRiI+PC9wYXRoPjxwYXRoIGQ9Ik00NC43MDU1IDIxLjg3NTRDNDQuNzA1NSAyMS44NzU0IDQ0LjcwNTUgMjAuOTU0OSA0Mi44MzEyIDIwLjY3ODdDNDIuMDY2NyAyMC41NjU2IDM3LjY0ODEgMjAuNjE3NCAzNy42NDgxIDIwLjYxNzRMMzYuMjczNCAxNy41ODRDMzYuMjczNCAxNy41ODQgNDIuMzY4MyAxNy42NTQxIDQ0LjI4ODIgMTguNTkxM0M0Ni4yMDgyIDE5LjUyODUgNDUuNzgwNCAyMC45OTA4IDQ1LjYwNSAyMi40OSIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik0xNy4wNjExIDIzLjY3MzlDMTcuMDYxMSAyMy42NzM5IDE2LjkzOTMgMjIuNzYxMyAxOC43NjQ2IDIyLjI0NEMxOS41MDggMjIuMDMzNiAyMy44OTU5IDIxLjUxMiAyMy44OTU5IDIxLjUxMkwyNS42ODc5IDE4LjI3MzRDMjUuNjg3OSAxOC4yNzM0IDE4LjgzMTIgMTkuMTg2MSAxNy4wNDg5IDIwLjM2MzVDMTUuMjY2NSAyMS41NDA5IDE1Ljg4MjggMjIuOTM0IDE2LjI0OTMgMjQuMzk2MyIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik0yMy42NjcxIDQ1LjY3NjJDMjMuNTUzMSA0Ni4wMjY5IDI0LjA1MjggNDYuNTY3IDI0LjIzNzggNDYuNzk5M0MyNS42OTQgNDguNjI3MiAyNi44MzQ2IDUwLjY4MTMgMjguMTE4MSA1Mi42MzM3QzI4LjY3NzQgNTMuNDg1IDMyLjIzMjUgNTguNzA2NiAzMy4zMjIyIDU3LjU0NzZDMzUuMTc3MyA1NS41NzUgMzYuNTQ0MSA1My4xNDIyIDM3Ljg4MSA1MC43OTdDMzkuMjgzOCA0OC4zMzUzIDQwLjUyODcgNDUuNzY0OCA0MS4zMDk4IDQzLjAzMzlDNDEuMzQxOCA0Mi45NDMxIDQxLjM1NTUgNDIuODQ2OCA0MS4zNTAxIDQyLjc1MDdDNDEuMjYyNSA0Mi4wMTM0IDM4LjA4MTggNDEuOTUyOSAzNy40Nzk1IDQxLjg2MDhMMzUuNjU0MiA0MS41ODAzQzM1LjQ4OTQgNDEuNTU0OSAzNS4wNjg2IDQxLjQwNDkgMzQuOTA2NCA0MS40NjU0TDI2LjM3MTcgNDQuNjM0N0wyNC42Nzg4IDQ1LjI2MzNDMjQuNDQ3NCA0NS4zNTEgMjMuODE3IDQ1LjM4NjkgMjMuNjkxNiA0NS42MjM2QzIzLjY4MTkgNDUuNjQwNCAyMy42NzM3IDQ1LjY1OCAyMy42NjcxIDQ1LjY3NjJaIiBmaWxsPSIjQjI2NzM0Ij48L3BhdGg+PHBhdGggZD0iTTI0LjkwMTkgNDYuMzI0N0wyOS45NDM4IDU0LjI2MjNMMzEuMTgxNyA1NS4zNTM4TDMyLjczNjkgNTYuNzEyN0wzNC4yNzk5IDU1LjE1MjJMMzguNjkxNSA0OC4yMjU0TDQwLjk5MDIgNDIuNzM5TDM0LjU4MTUgNDEuODUzNUwyNC45MDE5IDQ2LjMyNDdaIiBmaWxsPSIjQTQ1NzFGIj48L3BhdGg+PHBhdGggZD0iTTM3LjczNzQgNDguNjU2NUwyNy4wMDQ5IDQ4LjEyN0wyOS43ODc1IDUzLjgyNDZMMzAuODQ3NSA1NC44ODQ2TDMyLjMwNDUgNTYuMDc2OUwzMy4yMzIxIDU1LjI4MTdMMzMuODk0OSA1NC42MTk4TDM0LjU1NzYgNTMuNjkyMiIgZmlsbD0iI0NCNjNEMyI+PC9wYXRoPjxwYXRoIGQ9Ik0zMC4wNTIyIDUyLjMyMjNMMzAuOTc5OCA1My45MTI3TDMyLjE3MjEgNTUuMTA1TDM2LjY2MTcgNTEuMTNMMzEuNTEwMiA1MC41OTk2TDMwLjA1MjIgNTIuMzIyM1oiIGZpbGw9IiNCMjVGOEMiPjwvcGF0aD48cGF0aCBkPSJNMzMuMzkxNSAwLjI1MTA3NkMzMi45ODQ3IDAuMzA2MzA4IDMyLjUzOTMgMC43OTcyNiAzMi4zMzk1IDEuMDQwMTFDMjkuNTA3NyA0LjQ4OTkyIDI3Ljc4NjcgOC43MDMzNCAyNi4yOTkgMTIuOTEwNkMyNS40OTE1IDE1LjE5ODggMjQuNzM5MyAxNy41MDk4IDIzLjg2MjYgMTkuNzc3OEMyMy4wMTIyIDIxLjk5MDYgMjEuODI3OCAyNC4wNzM2IDIxLjA3MTIgMjYuMzAyMkMyMC4yMTQ3IDI4LjgyNTQgMTkuOTA2MSAzMS41MjMgMjAuMDUzNCAzNC4xODAyQzIwLjI2NTUgMzguMDA5NyAyMC44MDY1IDQyLjc2MjMgMjMuNTgwMyA0NS42NzY0QzI3Ljg0MjkgNTAuMTUzNyAzMC44ODMzIDUzLjMzNDQgMzAuODgzMyA1My4zMzQ0TDMyLjY5NTQgNTQuNzY1MkwzNC41NDc5IDUzLjA5OTRDMzQuNTQ3OSA1My4wOTk0IDM5LjQ5NDIgNDguNzA4IDQwLjAzMTYgNDYuNzY1M0M0MC41NjkgNDQuODIyNSA0MC41OTYyIDQ0Ljk2NDUgNDEuMDUxMiA0My45Mzg4QzQxLjUwNjIgNDIuOTEzIDQyLjI0NTMgNDIuODQ0NyA0Mi44MDQ2IDM5Ljk4NjZDNDQuMTU1NiAzMy4xMDcyIDQxLjczMDcgMjYuMTA1IDM5LjMzNDYgMTkuNzEyMUMzNy45NzkzIDE2LjA5MzkgMzYuNTM4OCAxMi40OTI0IDM1LjYyMDkgOC43MzkyOEMzNS4yMjU1IDcuMTIwMDIgMzQuOTI5MiA1LjQ3OTcxIDM0LjU5NyAzLjg0NjQyQzM0LjQzMDQgMy4wMzEwOSAzNC4yNTY4IDIuMjE2NjQgMzQuMDU3OCAxLjQwODMyQzMzLjk4NzYgMS4xMjUxNSAzMy45NTk2IDAuNDI5OTIzIDMzLjY1NDUgMC4yODc4OThDMzMuNTcyMSAwLjI1MDc4MSAzMy40ODA5IDAuMjM4MDA1IDMzLjM5MTUgMC4yNTEwNzZaIiBmaWxsPSIjQkQ2RTM1Ij48L3BhdGg+PHBhdGggZD0iTTMzLjc0NjYgMC4zNTI3NTNDMzQuMDYwNCAwLjc2MjE3MiAzMy41ODM1IDEuMDc2MDMgMzMuNjI5MSAxLjU3MDQ5QzMzLjY4NDYgMi4xNjg0IDMzLjc0NzcgMi43NjU3MiAzMy44MTg1IDMuMzYyNDZDMzMuOTY1NyA0LjU5MzM1IDM0LjE0NjMgNS44MTkyNyAzNC4zNjAzIDcuMDQwMjJDMzQuNzgyIDkuNDQ0NjggMzUuMzMzMyAxMS44MjQ2IDM2LjAxMiAxNC4xNjk1QzM3Ljg4NzIgMjAuNjQ2NiA0MC43NTQgMjYuOTc3MiA0MC45MjE1IDMzLjcxOTlDNDEuMDk2OCA0MC41OTQyIDM4LjM5NTcgNDcuMjgyNSAzNC42Mjc2IDUzLjAzNTRDMzguNjYwNSA0OS41NzUxIDQxLjYxNTggNDUuNzAyNyA0Mi44OTA1IDM5LjY0NTZDNDMuNTk5OCAzNi4yODA4IDQzLjI2MTQgMzIuNjcyMyA0Mi41MjE1IDI5LjMxNjNDNDIuMTU5NCAyNy42NzUxIDQxLjM1MTEgMjYuMTkgNDAuOTE4IDI0LjU5MDlDNDAuNDQzNyAyMi44Mzc1IDM5Ljg3MjEgMjEuMTA3NyAzOS4yNjcxIDE5LjM5MzhDMzguMDU2NCAxNS45NjI0IDM2LjcwMjggMTIuNTc4MyAzNS42ODY3IDkuMDgxMThDMzUuMDExNiA2Ljc1NzkyIDM0Ljc5MTYgNC4zMjk0NiAzNC4yMDI1IDEuOTc5OTFDMzQuMTYzIDEuODI1NjEgMzMuOTg1OSAtMC4wMTEwNzcyIDMzLjM5MDYgMC4yOTEzODQiIGZpbGw9IiNCMjY3MzQiPjwvcGF0aD48cGF0aCBkPSJNMjYuNTg5MyAzNy4zMzI2QzI2LjY1NDIgMzguMzM0NyAyNS45NjQyIDM5LjE5NTYgMjUuMDQ3MiAzOS4yNTUyQzI0LjEzMDEgMzkuMzE0OSAyMy4zMzUgMzguNTUzOSAyMy4yNjkyIDM3LjU0ODNDMjMuMjAzNSAzNi41NDI3IDIzLjk0MDggMzYuMzk2MyAyNC44NTc4IDM2LjMzNjdDMjUuNzc0OCAzNi4yNzcxIDI2LjUyMzUgMzYuMzMyMyAyNi41ODkzIDM3LjMzMjZaIiBmaWxsPSJ3aGl0ZSI+PC9wYXRoPjxwYXRoIGQ9Ik00MS4xMzM4IDM2LjQ2NjJDNDEuMjAyMiAzNy41MTI5IDQwLjQ0MjkgMzguNDEzMyAzOS40MzkxIDM4LjQ4MjZDMzguNDM1MyAzOC41NTE4IDM3LjU2NTYgMzcuNzUyMyAzNy40OTgxIDM2LjcwNjRDMzcuNDMwNiAzNS42NjA1IDM4LjIzNDUgMzUuNDcwMiAzOS4yMzkyIDM1LjQwNTRDNDAuMjQzOSAzNS4zNDA1IDQxLjA2NDUgMzUuNDIwMyA0MS4xMzM4IDM2LjQ2NjJaIiBmaWxsPSJ3aGl0ZSI+PC9wYXRoPjxwYXRoIGQ9Ik0yOS4zOTU1IDQ1LjgxNzJDMjkuNjMzNCA0NS43Mzk2IDI5LjcwMjkgNDUuMjk4MyAyOS41NTA2IDQ0LjgzMTVDMjkuMzk4MyA0NC4zNjQ4IDI5LjA4MiA0NC4wNDkzIDI4Ljg0NCA0NC4xMjdDMjguNjA2IDQ0LjIwNDYgMjguNTM2NSA0NC42NDYgMjguNjg4OCA0NS4xMTI3QzI4Ljg0MTEgNDUuNTc5NSAyOS4xNTc1IDQ1Ljg5NDkgMjkuMzk1NSA0NS44MTcyWiIgZmlsbD0iIzkxNTYyRCI+PC9wYXRoPjxwYXRoIGQ9Ik0zNS44NzY4IDQ1LjEwMDFDMzYuMDI5MSA0NC42MzM0IDM1Ljk1OTYgNDQuMTkyIDM1LjcyMTYgNDQuMTE0NEMzNS40ODM3IDQ0LjAzNjggMzUuMTY3MyA0NC4zNTIyIDM1LjAxNSA0NC44MTg5QzM0Ljg2MjcgNDUuMjg1NyAzNC45MzIyIDQ1LjcyNyAzNS4xNzAyIDQ1LjgwNDdDMzUuNDA4MSA0NS44ODIzIDM1LjcyNDUgNDUuNTY2OSAzNS44NzY4IDQ1LjEwMDFaIiBmaWxsPSIjOTE1NjJEIj48L3BhdGg+PHBhdGggZD0iTTI1LjQzNjYgMzguOTAxNUMyNS44MDggMzguOTAxNSAyNi4xMDkgMzguNjAwNCAyNi4xMDkgMzguMjI5MUMyNi4xMDkgMzcuODU3NyAyNS44MDggMzcuNTU2NiAyNS40MzY2IDM3LjU1NjZDMjUuMDY1MiAzNy41NTY2IDI0Ljc2NDIgMzcuODU3NyAyNC43NjQyIDM4LjIyOTFDMjQuNzY0MiAzOC42MDA0IDI1LjA2NTIgMzguOTAxNSAyNS40MzY2IDM4LjkwMTVaIiBmaWxsPSIjMjMyMzIzIj48L3BhdGg+PHBhdGggZD0iTTM4Ljk1NzMgMzguMDEzMkMzOS4zMzI1IDM4LjAxMzIgMzkuNjM2NyAzNy43MDkgMzkuNjM2NyAzNy4zMzM3QzM5LjYzNjcgMzYuOTU4NSAzOS4zMzI1IDM2LjY1NDMgMzguOTU3MyAzNi42NTQzQzM4LjU4MiAzNi42NTQzIDM4LjI3NzggMzYuOTU4NSAzOC4yNzc4IDM3LjMzMzdDMzguMjc3OCAzNy43MDkgMzguNTgyIDM4LjAxMzIgMzguOTU3MyAzOC4wMTMyWiIgZmlsbD0iIzIzMjMyMyI+PC9wYXRoPjxwYXRoIGQ9Ik00NC4yMzY0IDI4LjkyMDlMNDIuNTAxNSAyOS4wMzM3QzQyLjMyMzIgMjkuMDQ1MyA0Mi4xODgxIDI5LjE5OTIgNDIuMTk5NyAyOS4zNzc1TDQyLjg0NTUgMzkuMzA5N0M0Mi44NTcxIDM5LjQ4OCA0My4wMTEgMzkuNjIzMSA0My4xODkzIDM5LjYxMTVMNDQuOTI0MSAzOS40OTg3QzQ1LjEwMjQgMzkuNDg3MiA0NS4yMzc1IDM5LjMzMzIgNDUuMjI1OSAzOS4xNTQ5TDQ0LjU4MDIgMjkuMjIyN0M0NC41Njg2IDI5LjA0NDQgNDQuNDE0NiAyOC45MDkzIDQ0LjIzNjQgMjguOTIwOVoiIGZpbGw9IiMzRjNGM0YiPjwvcGF0aD48cGF0aCBkPSJNNDUuNzg0MiAyOS4zNTA4TDQ0Ljk5MDcgMjkuNDAyM0w0NS42MDExIDM4Ljc5MDRMNDYuMzk0NiAzOC43Mzg4TDQ1Ljc4NDIgMjkuMzUwOFoiIGZpbGw9IiMxNjE2MTYiPjwvcGF0aD48cGF0aCBkPSJNNDYuODg2NiAyOS45NDI4TDQ2LjA4OTYgMjkuOTk0N0M0NS45NDQ3IDMwLjAwNDEgNDUuODM0OCAzMC4xMjkyIDQ1Ljg0NDIgMzAuMjc0Mkw0Ni4zNTE3IDM4LjA3OTZDNDYuMzYxMSAzOC4yMjQ2IDQ2LjQ4NjMgMzguMzM0NCA0Ni42MzEyIDM4LjMyNUw0Ny40MjgyIDM4LjI3MzJDNDcuNTczMiAzOC4yNjM4IDQ3LjY4MyAzOC4xMzg2IDQ3LjY3MzYgMzcuOTkzN0w0Ny4xNjYxIDMwLjE4ODJDNDcuMTU2NyAzMC4wNDMzIDQ3LjAzMTUgMjkuOTMzNCA0Ni44ODY2IDI5Ljk0MjhaIiBmaWxsPSIjMTYxNjE2Ij48L3BhdGg+PHBhdGggZD0iTTQ0LjQxMjkgMzkuNzk0NUw0NC4zNTI0IDM3LjkzOTRMNDQuODI5MyAzNy4xMTE4TDQ0LjM1NjggMjkuODM5Nkw0My41Njc4IDI4Ljk2MTFMNDMuNDg1NCAyOC43MDA4TDQ0LjUzNDggMjguNUw0NS4yMjIxIDI4Ljg1MzNMNDUuODkyOCAzOS4xNjc3TDQ1LjI2NTkgMzkuNzM5M0w0NC40MTI5IDM5Ljc5NDVaIiBmaWxsPSIjMzAzMDMwIj48L3BhdGg+PHBhdGggZD0iTTQ1LjMzNTkgMjguMzE0NUw0NS43MTk5IDI5LjM1MTZMNDYuMzQ4NSAzOS4wMDQxTDQ1LjkyNjggMzkuNjk1OEw0Ny4wOTk5IDM5LjM1MzlMNDYuNDExNiAyOC43NzU2TDQ1LjMzNTkgMjguMzE0NVoiIGZpbGw9IiMzMDMwMzAiPjwvcGF0aD48cGF0aCBkPSJNNDQuNzIxNyAyMS44NjUyTDQ1LjYyNTYgMjIuMjQ1N0w0Ni4wOTIgMjguMDk1OUw0NS4yOTc3IDI4LjIyMjJMNDQuNzIxNyAyMS44NjUyWiIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik00NS4zMTk3IDI4LjMxNTdMNDUuMzE0NSAyNi4xOTE0TDQ2LjQxMjEgMjguNzc1OUw0NS4zMTk3IDI4LjMxNTdaIiBmaWxsPSIjMzAzMDMwIj48L3BhdGg+PHBhdGggZD0iTTE2LjA2MjcgMzcuNTIzOUwxNS42MjE3IDM3LjU1MjZDMTUuNDg3OSAzNy41NjEzIDE1LjM4NjQgMzcuNjc2OSAxNS4zOTUyIDM3LjgxMDdMMTUuNDkyNiAzOS4zMDkzQzE1LjUwMTMgMzkuNDQzMiAxNS42MTY4IDM5LjU0NDYgMTUuNzUwNyAzOS41MzU5TDE2LjE5MTYgMzkuNTA3MkMxNi4zMjU0IDM5LjQ5ODUgMTYuNDI2OSAzOS4zODMgMTYuNDE4MiAzOS4yNDkxTDE2LjMyMDcgMzcuNzUwNUMxNi4zMTIgMzcuNjE2NyAxNi4xOTY1IDM3LjUxNTIgMTYuMDYyNyAzNy41MjM5WiIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik0xNS4zMzYxIDM2LjkwNjJDMTUuMzM2MSAzNi45MDYyIDguNjMzNzIgNTYuNDYzNyAyNi42MjQ1IDU1LjI5MzNMMjcuMDE4MSA1NS4yNjk2TDI2Ljk0MSA1NC4wNzlDMjYuOTQxIDU0LjA3OSAxMi4yNzk5IDU3LjI4NiAxNS45MDUxIDM5LjUyNUMxNi40ODYzIDM2LjY3NzQgMTUuNzQxMSAzNy4wMTIzIDE1Ljc0MTEgMzcuMDEyM0wxNS4zMzYxIDM2LjkwNjJaIiBmaWxsPSIjMzAzMDMwIj48L3BhdGg+PHBhdGggZD0iTTE5LjEyNTkgNDEuMTcyOEwyMC44NjA4IDQxLjA2MDFDMjEuMDM5MSA0MS4wNDg1IDIxLjE3NDIgNDAuODk0NSAyMS4xNjI2IDQwLjcxNjJMMjAuNTE2OCAzMC43ODRDMjAuNTA1MiAzMC42MDU3IDIwLjM1MTMgMzAuNDcwNiAyMC4xNzMgMzAuNDgyMkwxOC40MzgyIDMwLjU5NUMxOC4yNTk5IDMwLjYwNjYgMTguMTI0OCAzMC43NjA1IDE4LjEzNjQgMzAuOTM4OEwxOC43ODIxIDQwLjg3MUMxOC43OTM3IDQxLjA0OTMgMTguOTQ3NyA0MS4xODQ0IDE5LjEyNTkgNDEuMTcyOFoiIGZpbGw9IiMzRjNGM0YiPjwvcGF0aD48cGF0aCBkPSJNMTcuNTcwOCA0MC42MTAyTDE4LjM2NDMgNDAuNTU4NkwxNy43NTM5IDMxLjE3MDVMMTYuOTYwNCAzMS4yMjIxTDE3LjU3MDggNDAuNjEwMloiIGZpbGw9IiMxNjE2MTYiPjwvcGF0aD48cGF0aCBkPSJNMTYuNDg1NSA0MC4yODE4TDE3LjI4MjUgNDAuMjNDMTcuNDI3NCA0MC4yMjA1IDE3LjUzNzMgNDAuMDk1NCAxNy41Mjc5IDM5Ljk1MDRMMTcuMDIwNCAzMi4xNDVDMTcuMDEwOSAzMi4wMDAxIDE2Ljg4NTggMzEuODkwMiAxNi43NDA4IDMxLjg5OTZMMTUuOTQzOSAzMS45NTE0QzE1Ljc5ODkgMzEuOTYwOSAxNS42ODkgMzIuMDg2IDE1LjY5ODUgMzIuMjMwOUwxNi4yMDYgNDAuMDM2NEMxNi4yMTU0IDQwLjE4MTMgMTYuMzQwNSA0MC4yOTEyIDE2LjQ4NTUgNDAuMjgxOFoiIGZpbGw9IiMxNjE2MTYiPjwvcGF0aD48cGF0aCBkPSJNMTkuNjczNCA0MS40MDQ4TDE5LjQ5MzcgMzkuNTU3NkwxOC45MTMzIDM4Ljc5ODNMMTguNDM5OSAzMS41MjYxTDE5LjEwNzkgMzAuNTUzTDE5LjE1NjIgMzAuMjgzOEwxOC4wODkyIDMwLjIyMDdMMTcuNDUzNiAzMC42NjA4TDE4LjEyNTIgNDAuOTc0M0wxOC44MjA0IDQxLjQ2TDE5LjY3MzQgNDEuNDA0OFoiIGZpbGw9IiMzMDMwMzAiPjwvcGF0aD48cGF0aCBkPSJNMTcuMjcxNSAzMC4xNDA2TDE3LjAyNDMgMzEuMjE5TDE3LjY1MiA0MC44NzIzTDE4LjE1OTYgNDEuNTAyN0wxNi45NTI0IDQxLjMxNTlMMTYuMjY0MiAzMC43Mzc3TDE3LjI3MTUgMzAuMTQwNloiIGZpbGw9IiMzMDMwMzAiPjwvcGF0aD48cGF0aCBkPSJNMTcuMDc3MyAyMy42NjQxTDE2LjI0MjcgMjQuMzMzOUwxNi40OTE3IDMwLjAyMTlMMTcuMjk2NSAzMC4wNDM4TDE3LjA3NzMgMjMuNjY0MVoiIGZpbGw9IiMzMDMwMzAiPjwvcGF0aD48cGF0aCBkPSJNMTcuMjg3MyAzMC4xMzk5TDE3LjAxODEgMjguMDMzMkwxNi4yNjQyIDMwLjczNzhMMTcuMjg3MyAzMC4xMzk5WiIgZmlsbD0iIzMwMzAzMCI+PC9wYXRoPjxwYXRoIGQ9Ik0yMC42NDQ1IDI4LjEwOTdDMjEuMjkzMiAyNy42NzEzIDIyLjEzNDkgMjcuNjEyNiAyMi44ODg4IDI3LjUxODhDMjMuNzY1NSAyNy40MTE4IDI0LjY0MjIgMjcuMzYwMSAyNS41MDA1IDI3LjU4ODlDMjYuMjQyMiAyNy43ODcgMjYuNTU5NiAyNi42MzY4IDI1LjgxNyAyNi40Mzg3QzI0LjggMjYuMTY3OCAyMy43NjM4IDI2LjIxMjUgMjIuNzI5MyAyNi4zNDY2QzIxLjgxMDUgMjYuNDY1OSAyMC44MzAzIDI2LjU0NDggMjAuMDQyMiAyNy4wNzk1QzE5LjQxMSAyNy41MDgyIDIwLjAwNzEgMjguNTQxOSAyMC42NDQ1IDI4LjEwOTdaIiBmaWxsPSIjN0M0NDFFIj48L3BhdGg+PHBhdGggZD0iTTM3LjE3NTIgMjcuMTU0N0MzOC41OTgxIDI2LjU2NzEgNDAuMTc2OSAyNi40NzMgNDEuNjU5NSAyNi44ODczQzQyLjM5OTUgMjcuMDk0MiA0Mi43MTE2IDI1Ljk0NCA0MS45NzYgMjUuNzM3MUM0MC4yODc2IDI1LjI2MDMgMzguNDg4OSAyNS4zNTQ2IDM2Ljg1OTYgMjYuMDA1NEMzNi41NTg5IDI2LjEyNjQgMzYuMzU0NiAyNi40MDE2IDM2LjQ0MzIgMjYuNzM4M0MzNi41MTg2IDI3LjAyMjQgMzYuODgxNSAyNy4yNzU3IDM3LjE3NyAyNy4xNTQ3SDM3LjE3NTJaIiBmaWxsPSIjN0M0NDFFIj48L3BhdGg+PHBhdGggZD0iTTMzLjk1ODMgMzUuNDZDMzMuOTI2NyAzNC44NDYzIDMyLjgxODYgMzQuMTA1NSAzMS41MDM1IDM0LjE3MjJDMzAuMTg4NSAzNC4yMzg4IDI5LjE1NzUgMzUuMDg0OCAyOS4xODgyIDM1LjcwMDJDMjkuMjE4OSAzNi4zMTU3IDMwLjI0MDIgMzUuMzkyNSAzMS41NjIzIDM1LjMyNjhDMzIuODg0MyAzNS4yNjEgMzMuOTg5IDM2LjA3NjQgMzMuOTU4MyAzNS40NloiIGZpbGw9IiMyMzIzMjMiPjwvcGF0aD48cGF0aCBkPSJNMjUuMTYxMyA0MS4xODgyQzI3LjczMjQgNDEuMDU4OCAyOS42NzE5IDM4LjA3MzQgMjkuNDkzMSAzNC41MjAxQzI5LjMxNDQgMzAuOTY2OCAyNy4wODUxIDI4LjE5MTEgMjQuNTEzOSAyOC4zMjA1QzIxLjk0MjcgMjguNDQ5OCAyMC4wMDMzIDMxLjQzNTIgMjAuMTgyMSAzNC45ODg1QzIwLjM2MDggMzguNTQxOSAyMi41OTAxIDQxLjMxNzUgMjUuMTYxMyA0MS4xODgyWiIgZmlsbD0id2hpdGUiPjwvcGF0aD48cGF0aCBkPSJNMzguNTUyNCA0MC41MTYzQzQxLjEyMzYgNDAuMzg3IDQzLjA2MyAzNy40MDE2IDQyLjg4NDIgMzMuODQ4MkM0Mi43MDU1IDMwLjI5NDkgNDAuNDc2MiAyNy41MTkzIDM3LjkwNSAyNy42NDg2QzM1LjMzMzkgMjcuNzc4IDMzLjM5NDQgMzAuNzYzMyAzMy41NzMyIDM0LjMxNjdDMzMuNzUxOSAzNy44NyAzNS45ODEyIDQwLjY0NTcgMzguNTUyNCA0MC41MTYzWiIgZmlsbD0id2hpdGUiPjwvcGF0aD48cGF0aCBkPSJNMjEuOTkzIDMwLjIwODFDMjEuMjczMyAzMS4yNDYxIDIwLjgzOTMgMzEuODA5IDIwLjM1NjIgMzEuODMzNUMxOS44NzMyIDMxLjg1ODEgMTkuNDA2OCAzMC45NzQ0IDE5LjM1NDIgMjkuOTE3OUMxOS4zMDE2IDI4Ljg2MTUgMjIuMzgxNCAyOC43MDQ2IDIyLjg2ODkgMjguNjhDMjMuMzU2MyAyOC42NTU1IDIyLjE1ODcgMjkuOTY3OSAyMS45OTMgMzAuMjA4MVoiIGZpbGw9IiMyMzIzMjMiPjwvcGF0aD48cGF0aCBkPSJNNDAuMzYzNSAyOS4yODUxQzQxLjE4MzIgMzAuMjQ5NSA0MS42NzE1IDMwLjc2MjQgNDIuMTU5IDMwLjczNzhDNDIuNjQ2NCAzMC43MTMzIDQzLjAxNzIgMjkuNzg4MyA0Mi45NjQ2IDI4LjczMTlDNDIuOTEyIDI3LjY3NTUgMzkuODMwNCAyNy44MjggMzkuMzQzIDI3Ljg1NTJDMzguODU1NSAyNy44ODI0IDQwLjE3MDYgMjkuMDYzMyA0MC4zNjM1IDI5LjI4NTFaIiBmaWxsPSIjMjMyMzIzIj48L3BhdGg+PHBhdGggZD0iTTI1LjAyNTMgNDAuMDg3OEMyNS44OTY5IDQwLjA4NzggMjYuNjAzNCAzOS4zODEyIDI2LjYwMzQgMzguNTA5N0MyNi42MDM0IDM3LjYzODIgMjUuODk2OSAzNi45MzE2IDI1LjAyNTMgMzYuOTMxNkMyNC4xNTM4IDM2LjkzMTYgMjMuNDQ3MyAzNy42MzgyIDIzLjQ0NzMgMzguNTA5N0MyMy40NDczIDM5LjM4MTIgMjQuMTUzOCA0MC4wODc4IDI1LjAyNTMgNDAuMDg3OFoiIGZpbGw9IiMyMzIzMjMiPjwvcGF0aD48cGF0aCBkPSJNMzguNjc4MiAzOS41NTE3QzM5LjYzMjUgMzkuNTUxNyA0MC40MDYxIDM4Ljc3OCA0MC40MDYxIDM3LjgyMzdDNDAuNDA2MSAzNi44NjkzIDM5LjYzMjUgMzYuMDk1NyAzOC42NzgyIDM2LjA5NTdDMzcuNzIzOCAzNi4wOTU3IDM2Ljk1MDIgMzYuODY5MyAzNi45NTAyIDM3LjgyMzdDMzYuOTUwMiAzOC43NzggMzcuNzIzOCAzOS41NTE3IDM4LjY3ODIgMzkuNTUxN1oiIGZpbGw9IiMyMzIzMjMiPjwvcGF0aD48cGF0aCBkPSJNMjUuMzY4NyAzOS42ODI4QzI1LjYwNzQgMzkuNjgyOCAyNS44MDA5IDM5LjQ4OTMgMjUuODAwOSAzOS4yNTA2QzI1LjgwMDkgMzkuMDExOSAyNS42MDc0IDM4LjgxODQgMjUuMzY4NyAzOC44MTg0QzI1LjEzIDM4LjgxODQgMjQuOTM2NSAzOS4wMTE5IDI0LjkzNjUgMzkuMjUwNkMyNC45MzY1IDM5LjQ4OTMgMjUuMTMgMzkuNjgyOCAyNS4zNjg3IDM5LjY4MjhaIiBmaWxsPSJ3aGl0ZSI+PC9wYXRoPjxwYXRoIGQ9Ik0zOC4yNDY5IDM5LjE0OEMzOC41MDgzIDM5LjE0OCAzOC43MjAzIDM4LjkzNjEgMzguNzIwMyAzOC42NzQ2QzM4LjcyMDMgMzguNDEzMSAzOC41MDgzIDM4LjIwMTIgMzguMjQ2OSAzOC4yMDEyQzM3Ljk4NTQgMzguMjAxMiAzNy43NzM0IDM4LjQxMzEgMzcuNzczNCAzOC42NzQ2QzM3Ljc3MzQgMzguOTM2MSAzNy45ODU0IDM5LjE0OCAzOC4yNDY5IDM5LjE0OFoiIGZpbGw9IndoaXRlIj48L3BhdGg+PHBhdGggZD0iTTE5Ljk3ODEgMjkuODgyM0MyMC4xMjgzIDI5LjgyOTMgMjAuMjMxMyAyOS43MzMgMjAuMjA4MSAyOS42NjczQzIwLjE4NDkgMjkuNjAxNSAyMC4wNDQzIDI5LjU5MTIgMTkuODk0MSAyOS42NDQyQzE5Ljc0MzggMjkuNjk3MyAxOS42NDA5IDI5Ljc5MzUgMTkuNjY0MSAyOS44NTkzQzE5LjY4NzMgMjkuOTI1IDE5LjgyNzkgMjkuOTM1MyAxOS45NzgxIDI5Ljg4MjNaIiBmaWxsPSIjRkZDMzNFIj48L3BhdGg+PHBhdGggZD0iTTQyLjYzNjQgMjguNzQ4M0M0Mi42NjY1IDI4LjY4NTQgNDIuNTc0NCAyOC41Nzg3IDQyLjQzMDcgMjguNTA5OUM0Mi4yODcgMjguNDQxMiA0Mi4xNDYxIDI4LjQzNjUgNDIuMTE2IDI4LjQ5OTRDNDIuMDg1OSAyOC41NjIzIDQyLjE3OCAyOC42NjkgNDIuMzIxNyAyOC43Mzc3QzQyLjQ2NTQgMjguODA2NSA0Mi42MDYzIDI4LjgxMTIgNDIuNjM2NCAyOC43NDgzWiIgZmlsbD0iI0ZGQzMzRSI+PC9wYXRoPjxwYXRoIGQ9Ik0xNy4xOTk4IDUwLjI0MDZDMTcuMTk5OCA1MC4yNDA2IDE2LjczMjYgNDkuMjAyNiAxNi42ODUyIDQ4Ljg2MjRDMTYuNjczOCA0OC43ODE4IDE3LjI1OTUgNDguNzUyIDE3LjI1OTUgNDguNzUyTDE3Ljk4ODkgNTAuMDk2OEwxNy4xOTk4IDUwLjI0MDZaIiBmaWxsPSIjODI0MDA5Ij48L3BhdGg+PC9zdmc+',
)]
class Pterodactyl extends Server
{
    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'host',
                'label' => 'Pterodactyl URL',
                'type' => 'text',
                'description' => 'Pterodactyl URL',
                'required' => true,
                'validation' => 'url',
            ],
            [
                'name' => 'api_key',
                'label' => 'Pterodactyl API Key',
                'type' => 'text',
                'description' => 'Pterodactyl API Key',
                'required' => true,
                'encrypted' => true,
            ],
        ];
    }

    public function testConfig(): bool|string
    {
        try {
            $this->request('/api/application/servers', 'GET');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function request($url, $method = 'get', $data = []): array
    {
        // Trim any leading slashes from the base url and add the path URL to it
        $req_url = rtrim($this->config('host'), '/') . $url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('api_key'),
            'Accept' => 'application/json',
        ])->$method($req_url, $data);

        if (!$response->successful()) {
            throw new Exception($response->json()['errors'][0]['detail']);
        }

        return $response->json() ?? [];
    }

    public function getProductConfig($values = []): array
    {
        $nodes = $this->request('/api/application/nodes');
        $nodeList = [];
        foreach ($nodes['data'] as $node) {
            $nodeList[$node['attributes']['id']] = $node['attributes']['name'];
        }

        $location = $this->request('/api/application/locations');
        $locationList = [];
        foreach ($location['data'] as $location) {
            $locationList[$location['attributes']['id']] = $location['attributes']['short'];
        }

        $nests = $this->request('/api/application/nests');
        $nestList = [];
        foreach ($nests['data'] as $nest) {
            $nestList[$nest['attributes']['id']] = $nest['attributes']['name'];
        }

        $eggList = [];
        if (isset($values['nest_id']) && $values['nest_id'] !== '') {
            try {
                $eggs = $this->request('/api/application/nests/' . $values['nest_id'] . '/eggs');
                foreach ($eggs['data'] as $egg) {
                    $eggList[$egg['attributes']['id']] = $egg['attributes']['name'];
                }
            } catch (Exception $e) {
            }
        }

        $using_port_array = isset($values['port_array']) && $values['port_array'] !== '';

        return [
            [
                'name' => 'location_ids',
                'label' => 'Location(s)',
                'type' => 'select',
                'description' => 'Location(s) where the server will be installed',
                'options' => $locationList,
                'multiple' => true,
                'database_type' => 'array',
                'required' => false,
            ],
            [
                'name' => 'node',
                'label' => 'Node',
                'type' => 'select',
                'description' => 'Fill in to install the server on a specific node',
                'options' => $nodeList,
            ],
            [
                'name' => 'nest_id',
                'label' => 'Nest ID',
                'type' => 'select',
                'options' => $nestList,
                'description' => 'Nest ID to fetch the eggs from',
                'required' => true,
                // Lets fetch the eggs every time the nest id changes
                'live' => true,
            ],
            [
                'name' => 'egg_id',
                'label' => 'Egg ID',
                'type' => 'select',
                'options' => $eggList,
                'required' => true,
            ],
            [
                'name' => 'memory',
                'label' => 'Memory',
                'type' => 'number',
                'suffix' => 'MiB',
                'required' => true,
                'validation' => 'numeric',
                'min_value' => 0,
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'swap',
                'label' => 'Swap',
                'type' => 'number',
                'min_value' => -1,
                'suffix' => 'MiB',
                'required' => true,
                'description' => 'Set to -1 for unlimited, or to 0 to disable swap',
            ],
            [
                'name' => 'disk',
                'label' => 'Disk',
                'type' => 'number',
                'suffix' => 'MiB',
                'required' => true,
                'min_value' => 0,
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'io',
                'label' => 'IO Weight',
                'type' => 'number',
                'required' => true,
                'default' => 500,
                'min_value' => 10,
                'max_value' => 1000,
                'description' => 'The IO Weight is the priority given to this server for disk access.',
                'hint' => new HtmlString('<a href="https://docs.docker.com/engine/reference/run/#block-io-bandwidth-blkio-constraint" target="_blank">Documentation</a>'),
            ],
            [
                'name' => 'cpu',
                'label' => 'CPU Limit',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
                'suffix' => '%',
                'description' => 'Set to 0 for unlimited',
            ],
            [
                'name' => 'cpu_pinning',
                'label' => 'CPU Pinning',
                'type' => 'text',
                'description' => 'Leave empty for no pinning. Used to specify what threads should be used. Example: 0,2-4,5,6',
                'validation' => 'regex:/^[0-9]+(?:-[0-9]+)?(?:,[0-9]+(?:-[0-9]+)?)*$/',
            ],
            [
                'name' => 'databases',
                'label' => 'Databases',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'backups',
                'label' => 'Backups',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'additional_allocations',
                'label' => 'Additional Allocations',
                'type' => 'number',
                'required' => true,
                'min_value' => 0,
            ],
            [
                'name' => 'port_array',
                'label' => 'Port Array',
                'type' => 'text',
                'description' => 'Used to assign ports to egg variables.',
                'hint' => new HtmlString('<a href="https://paymenter.org/docs/extensions/pterodactyl#port-array" target="_blank">Documentation</a>'),
                'live' => true,
                'validation' => 'json',
            ],
            [
                'name' => 'port_range',
                'label' => 'Port ranges',
                'type' => 'tags',
                'description' => '',
                'database_type' => 'array',
                'required' => false,
                'disabled' => $using_port_array,
            ],
            [
                'name' => 'skip_scripts',
                'label' => 'Skip Egg Install Script',
                'description' => 'If the selected Egg has an install script attached to it, the script will run during the install. If you would like to skip this step, check this box.',
                'type' => 'checkbox',
            ],
            [
                'name' => 'dedicated_ip',
                'label' => 'Dedicated IP',
                'description' => 'Assigns the server an allocation whose IP is not being used by any other server.',
                'type' => 'checkbox',
                'disabled' => $using_port_array,
            ],
            [
                'name' => 'start_on_completion',
                'label' => 'Start on completion',
                'description' => 'Start server automatically after installation.',
                'type' => 'checkbox',
            ],
            [
                'name' => 'oom_killer',
                'label' => 'Enable OOM Killer',
                'description' => 'Terminates the server if it breaches the memory limits. Enabling OOM killer may cause server processes to exit unexpectedly.',
                'type' => 'checkbox',
            ],
        ];
    }

    public function createServer(Service $service, $settings, $properties)
    {
        if ($this->getServer($service->id, failIfNotFound: false)) {
            throw new Exception('Server already exists');
        }
        // Smash the properties into the settings
        $settings = array_merge($settings, $properties);

        $eggData = $this->request('/api/application/nests/' . $settings['nest_id'] . '/eggs/' . $settings['egg_id'], data: ['include' => 'variables']);
        if (!isset($eggData['attributes'])) {
            throw new Exception('Could not fetch egg data');
        }
        $environment = [];
        foreach ($eggData['attributes']['relationships']['variables']['data'] as $variable) {
            $environment[$variable['attributes']['env_variable']] = $settings[$variable['attributes']['env_variable']] ?? $variable['attributes']['default_value'];
        }

        $orderUser = $service->user;
        // Get the user id if one already exists...
        $user = $this->request('/api/application/users', 'get', ['filter' => ['email' => $orderUser->email]])['data'][0]['attributes']['id'] ?? null;

        // Otherwise create a new user
        if (!$user) {
            $user = $this->request('/api/application/users', 'post', [
                'email' => $orderUser->email,
                'username' => (preg_replace('/[^a-zA-Z0-9]/', '', strtolower(Str::transliterate($orderUser->name))) ?? Str::random(8)) . '_' . Str::random(4),
                'first_name' => $orderUser->first_name ?? '',
                'last_name' => $orderUser->last_name ?? '',
            ])['attributes']['id'];

            $returnData['created_user'] = true;
        }

        if (isset($settings['location'])) {
            $settings['location_ids'] = [$settings['location']];
        }

        $deploymentData = $this->generateDeploymentData($settings, $environment);

        $serverCreationData = [
            'external_id' => (string) $service->id,
            'name' => isset($settings['servername']) ? $settings['servername'] : $service->product->name . ' #' . $service->id,
            'user' => (int) $user,
            'egg' => $settings['egg_id'],
            'docker_image' => isset($settings['docker_image']) ? $settings['docker_image'] : $eggData['attributes']['docker_image'],
            'startup' => $eggData['attributes']['startup'],
            'environment' => $deploymentData['environment'],
            'skip_scripts' => $settings['skip_scripts'] ?? false,
            'oom_disabled' => !($settings['oom_killer'] ?? false),
            'limits' => [
                'memory' => (int) $settings['memory'],
                'swap' => (int) $settings['swap'],
                'disk' => (int) $settings['disk'],
                'io' => (int) $settings['io'],
                'threads' => $settings['cpu_pinning'] ?? null,
                'cpu' => (int) $settings['cpu'],
            ],
            'feature_limits' => [
                'databases' => (int) $settings['databases'],
                'allocations' => $deploymentData['allocations_needed'] + (int) $settings['additional_allocations'],
                'backups' => (int) $settings['backups'],
            ],
            'start_on_completion' => $settings['start_on_completion'] ?? false,
        ];
        if ($deploymentData['auto_deploy']) {
            $serverCreationData['deploy'] = [
                'locations' => (array) $settings['location_ids'],
                'dedicated_ip' => $settings['dedicated_ip'] ?? false,
                'port_range' => $settings['port_range'] ?? [],
            ];
        } else {
            $serverCreationData['allocation'] = $deploymentData['allocation'];
        }

        $server = $this->request('/api/application/servers', 'post', $serverCreationData);

        return [
            'server' => $server['attributes']['id'],
            'link' => $this->config('host') . '/server/' . $server['attributes']['identifier'],
        ];
    }

    private function generateDeploymentData($settings, $environment)
    {
        if (!isset($settings['port_array']) || $settings['port_array'] === '') {
            if ($settings['node']) {
                // Only get one allocation from the node
                $nodes = $this->request('/api/application/nodes/deployable', 'get', [
                    'memory' => $settings['memory'],
                    'disk' => $settings['disk'],
                    'location_ids' => $settings['location_ids'] ?? [],
                    'include' => ['allocations'],
                ]);
                $nodes = collect($nodes['data']);
                $nodes_by_id = $nodes->mapWithKeys(fn ($node) => [$node['attributes']['id'] => $node['attributes']]);

                if (!$nodes_by_id->has($settings['node'])) {
                    throw new Exception('Node is not suitable for deployment.');
                }
                $node = $nodes_by_id->get($settings['node']);
                $availablePorts = collect($node['relationships']['allocations']['data']);
                $availablePorts = $availablePorts
                    ->filter(fn ($port) => !$port['attributes']['assigned'])
                    ->map(
                        fn ($port) => [
                            'port' => $port['attributes']['port'],
                            'id' => $port['attributes']['id'],
                        ]
                    );
                if ($availablePorts->isEmpty()) {
                    throw new Exception('No available allocations found on the selected node.');
                }
                $allocation = $availablePorts->first();
                $environment['SERVER_PORT'] = $allocation['port'];

                // Return the allocation id for the SERVER_PORT
                return [
                    'auto_deploy' => false,
                    'environment' => $environment,
                    'allocations_needed' => 1,
                    'allocation' => [
                        'default' => $allocation['id'],
                        'additional' => [],
                    ],
                ];
            }

            return [
                'auto_deploy' => true,
                'environment' => $environment,
                'allocations_needed' => 1,
            ];
        }

        try {
            // Example: {"SERVER_PORT": 7777, "NONE": [7778, 7779], "QUERY_PORT": 2701, "RCON_PORT": 27020}
            $port_array = json_decode($settings['port_array'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
        } catch (Exception $e) {
            throw new Exception('Invalid JSON in port array');
        }

        if (!is_array($port_array)) {
            throw new Exception('Port array must be an array');
        }

        $nodes = $this->request('/api/application/nodes/deployable', 'get', [
            'memory' => $settings['memory'],
            'disk' => $settings['disk'],
            'location_ids' => $settings['location_ids'] ?? [],
            'include' => ['allocations'],
        ]);
        $nodes = collect($nodes['data']);
        $nodes_by_id = $nodes->mapWithKeys(fn ($node) => [$node['attributes']['id'] => $node['attributes']]);

        if ($settings['node']) {
            // If the product's node id is not in the deployable nodes array, throw error.
            if (!$nodes_by_id->has($settings['node'])) {
                throw new Exception('Node is not suitable for deployment.');
            }

            $node = $nodes_by_id->get($settings['node']);
            $availablePorts = collect($node['relationships']['allocations']['data']);
            $availablePorts = $availablePorts
                ->filter(fn ($port) => !$port['attributes']['assigned'])
                ->map(
                    fn ($port) => [
                        'port' => $port['attributes']['port'],
                        'id' => $port['attributes']['id'],
                    ]
                );

            $free_allocations_needed = 0;
            foreach ($port_array as $key => $value) {
                $free_allocations_needed += is_array($value) ? count($value) : 1;
            }

            if (count($availablePorts) < $free_allocations_needed) {
                throw new Exception("Not enough allocations found for deployment. Found: {$availablePorts->count()}, Required: {$free_allocations_needed}");
            }
        } else {
            foreach ($nodes as $index => $node) {
                $availablePorts = collect($node['attributes']['relationships']['allocations']['data']);
                $availablePorts = $availablePorts
                    ->filter(fn ($port) => !$port['attributes']['assigned'])
                    ->map(
                        fn ($port) => [
                            'port' => $port['attributes']['port'],
                            'id' => $port['attributes']['id'],
                        ]
                    );

                $free_allocations_needed = 0;
                foreach ($port_array as $key => $value) {
                    $free_allocations_needed += is_array($value) ? count($value) : 1;
                }

                if (count($availablePorts) < $free_allocations_needed) {
                    // If this was last viable node, throw error
                    if ($index == $nodes->count() - 1) {
                        throw new Exception('No nodes with suitable allocations found for deployment');
                    }

                    // Else move onto next viable node
                    continue;
                }
                break;
            }
        }

        $allocations = [];
        foreach ($port_array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $port) {
                    $allocation = $availablePorts->where('port', $port)->first();
                    if (!$allocation) {
                        // try to assign a higher port, if that fails try a random port
                        $allocation = $availablePorts->where('port', '>', $port)->first();
                        if (!$allocation) {
                            $allocation = $availablePorts->random();
                        }
                        if (!$allocation) {
                            throw new Exception('Could not find a port to assign');
                        }
                    }
                    $allocations[$key][] = $allocation;

                    // Remove the port from the available ports
                    $availablePorts = $availablePorts->reject(function ($port) use ($allocation) {
                        return $port['id'] == $allocation['id'];
                    });
                }
            } else {
                $allocation = $availablePorts->where('port', $value)->first();
                if (!$allocation) {
                    // try to assign a higher port, if that fails try a random port
                    $allocation = $availablePorts->where('port', '>', $value)->first();
                    if (!$allocation) {
                        $allocation = $availablePorts->random();
                    }
                    if (!$allocation) {
                        throw new Exception('Could not find a port to assign');
                    }
                }
                $allocations[$key] = $allocation;

                // Remove the port from the available ports
                $availablePorts = $availablePorts->reject(function ($port) use ($allocation) {
                    return $port['id'] == $allocation['id'];
                });
            }
        }

        $allocationIds = [];

        foreach ($allocations as $key => $value) {
            // Assign the allocations to the environment
            if ($key !== 'NONE') {
                if (isset($environment[$key])) {
                    $environment[$key] = $value['port'];
                }
            }

            // Set allocations to a array with only the ids
            if ($key !== 'SERVER_PORT') {
                if (is_array($value) && isset($value[0])) {
                    foreach ($value as $v) {
                        $allocationIds[] = $v['id'];
                    }
                } else {
                    $allocationIds[] = $value['id'];
                }
            }
        }

        return [
            'auto_deploy' => false,
            'allocations_needed' => $free_allocations_needed,
            'environment' => $environment,
            'allocation' => [
                'default' => $allocations['SERVER_PORT']['id'],
                'additional' => $allocationIds,
            ],
        ];
    }

    private function getServer($id, $failIfNotFound = true, $raw = false)
    {
        try {
            $response = $this->request('/api/application/servers/external/' . $id);
        } catch (Exception $e) {
            if ($failIfNotFound) {
                throw new Exception('Server not found');
            } else {
                return false;
            }
        }
        if ($raw) {
            return $response;
        }

        return $response['attributes']['id'] ?? false;
    }

    public function suspendServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server . '/suspend', 'post');

        return true;
    }

    public function unsuspendServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server . '/unsuspend', 'post');

        return true;
    }

    public function terminateServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id);

        $this->request('/api/application/servers/' . $server, 'delete');

        return true;
    }

    public function upgradeServer(Service $service, $settings, $properties)
    {
        $server = $this->getServer($service->id, raw: true);

        $settings = array_merge($settings, $properties);

        $updateServerData = [
            'allocation' => $server['attributes']['allocation'],
            'memory' => (int) $settings['memory'],
            'swap' => (int) $settings['swap'],
            'disk' => (int) $settings['disk'],
            'io' => (int) $settings['io'],
            'cpu' => (int) $settings['cpu'],
            'threads' => $settings['cpu_pinning'] ?? null,
            'feature_limits' => [
                'databases' => $settings['databases'],
                'allocations' => $settings['additional_allocations'],
                'backups' => $settings['backups'],
            ],
        ];

        $this->request('/api/application/servers/' . $server['attributes']['id'] . '/build', 'patch', $updateServerData);

        $eggData = $this->request('/api/application/nests/' . $settings['nest_id'] . '/eggs/' . $settings['egg_id'], data: ['include' => 'variables']);

        if (!isset($eggData['attributes'])) {
            throw new Exception('Could not fetch egg data');
        }

        $environment = [];

        foreach ($eggData['attributes']['relationships']['variables']['data'] as $variable) {
            // Check if variable has been set on server
            if (isset($server['attributes']['container']['environment'][$variable['attributes']['env_variable']])) {
                $environment[$variable['attributes']['env_variable']] = $server['attributes']['container']['environment'][$variable['attributes']['env_variable']];
            } else {
                $environment[$variable['attributes']['env_variable']] = $settings[$variable['attributes']['env_variable']] ?? $variable['attributes']['default_value'];
            }
        }

        $updateServerData = [
            'environment' => $environment,
            'skip_scripts' => $settings['skip_scripts'] ?? false,
            'oom_disabled' => !($settings['oom_killer'] ?? false),
            'egg' => $settings['egg_id'],
            'image' => $server['attributes']['container']['image'] ?? $eggData['attributes']['docker_image'],
            'startup' => $server['attributes']['container']['startup_command'] ?? $settings['startup'] ?? $eggData['attributes']['startup'],
        ];

        $this->request('/api/application/servers/' . $server['attributes']['id'] . '/startup', 'patch', $updateServerData);

        return true;
    }

    public function getActions(Service $service)
    {
        $server = $this->getServer($service->id, raw: true);

        return [
            [
                'type' => 'button',
                'label' => 'Go to server',
                'url' => $this->config('host') . '/server/' . $server['attributes']['identifier'],
            ],
        ];
    }
}
