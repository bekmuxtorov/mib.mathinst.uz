import datetime
from django.contrib import admin
from .models import Article, Issue

# Register your models here.
admin.site.site_header = 'Matematika Instituti Byulleteni'


class IssueAdmin(admin.ModelAdmin):
    def get_date(self, obj):
        return obj.created_at.strftime("%d %B, %Y")

    list_display = ["ordinal_number", "file_link", "get_date"]
    search_fields = ("name_uz",)


class ArticleAdmin(admin.ModelAdmin):
    fieldsets = (
        ("Jurnal", {
            "fields": (
                ("issue", "status", "ordinal_number")
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

    def get_date(self, obj):
        return obj.created_at.strftime("%d %B, %Y")

    list_display = ["ordinal_number", "name_uz", "get_date"]
    ordering = ("ordinal_number",)


admin.site.register(Article, ArticleAdmin)
admin.site.register(Issue, IssueAdmin)
