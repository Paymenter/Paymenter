# Hi there,

# This is a test email from {{ config('app.name') }}. 
## If you received this email, it means that your email settings are correct.

# Header 1
## Header 2
### Header 3
#### Header 4
##### Header 5
###### Header 6

# Unordered list
- Item 1
- Item 2
- Item 3

# Ordered list
1. Item 1
2. Item 2
3. Item 3

# Blockquote
> This is a blockquote

# Code
`This is a code`

# Code block
```
This is a code block
```

# Table
| Header 1 | Header 2 | Header 3 |
|:---------|:---------|:---------|
| Item 1   | Item 2   | Item 3   |
| Item 4   | Item 5   | Item 6   |

# Horizontal rule
---

# Link
[{{ config('app.name') }}]({{ config('app.url') }})
# Image
![{{ config('app.name') }}]({{ config('app.url') }}/img/logo.png)

# Button
@component('mail::button', ['url' => route('clients.home')])
    View order
@endcomponent



# Thanks,<br>
# {{ config('app.name') }}
