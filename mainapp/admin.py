from django.contrib import admin
from .models import Article, Issue

# Register your models here.
admin.site.site_header = 'Matematika Instituti Byulleteni'


class IssueAdmin(admin.ModelAdmin):
    list_display = ["id", "name_uz", "file_link", "created_at"]
    search_fields = ("name_uz",)


class ArticleAdmin(admin.ModelAdmin):
    fieldsets = (
        ("Jurnal", {
            "fields": (
                ("issue",)
            ),
            'classes': ('wide',)
        }),

        ("Maqola muallifi", {
            "fields": (
                ("author_uz", "author_en")
            ),
            'classes': ('wide',)
        }),

        ("Maqola", {
            "fields": (
                ("name_uz", "name_en", "keywords_uz",
                 "keywords_en", "short_data_uz", "short_data_en", "references")
            ),
            'classes': ('wide',)
        }),


        ("Maqola manzili", {
            "fields": (
                ("file_link", "first_page", "last_page")
            ),
            'classes': ('wide',)
        }),
    )

    # def get_form(self, request, obj=None, **kwargs):
    #     form = super(ArticleAdmin, self).get_form(request, obj, **kwargs)
    #     form.base_fields['name_uz'].widget.attrs['style'] = 'height: 22em;'
    #     return form

    list_display = ["id", "name_uz", "created_at"]


admin.site.register(Article, ArticleAdmin)
admin.site.register(Issue, IssueAdmin)
