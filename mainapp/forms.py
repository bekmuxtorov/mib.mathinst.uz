from django import forms


class Article(forms.Form):
    author_uz = forms.CharField(
        label="Muallif",
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        help_text="⚠️Har bir muallifni ';'(nuqtali vergul) bilan ajratib yozing<br>"
    )

    name_uz = forms.CharField(
        widget=forms.TextInput(
            attrs={"class": "form_control"}
        ),
        label="Nomi[uz]",
        max_length=200,
        help_text="Nomni tarkibida matematika formulalar mavjud bo'lsa,(\[...\], \(...\), $$...$$) Latex kalit so'zlaridan foydalanib yozishingiz mumkin."
    )

    name_en = forms.CharField(
        widget=forms.TextInput(
            attrs={"class": "form_control"}
        ),
        label="Nomi[en]",
        max_length=200,
        help_text="Nomni tarkibida matematika formulalar mavjud bo'lsa,(\[...\], \(...\), $$...$$) Latex kalit so'zlaridan foydalanib yozishingiz mumkin."
    )

    keywords_uz = forms.CharField(
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        label="Kalit so'zlar[uz]"
    )

    keywords_en = forms.CharField(
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        label="Kalit so'zlar[en]"
    )

    short_data_uz = forms.CharField(
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        label="Anatatsiya[uz]"
    )

    short_data_en = forms.CharField(
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        label="Anatatsiya[en]"
    )

    references = forms.CharField(
        widget=forms.Textarea(
            attrs={"rows": 5, "class": "form_control"}
        ),
        label="Foydalanilgan adabiyotlar",
        help_text="⚠️Har bir adabiyotni ';'(nuqtali vergul) bilan ajratib yozing.<br>"
    )

    file_link = forms.URLField(
        widget=forms.URLInput(
            attrs={"class": "form_control"}
        ),
        label="Kitob manzili",
        help_text="⚠️Yuqoridagi namunadagi matlab faylni to'ldirib, google drivega joylab uni linkini shu yerga joylang."
    )
